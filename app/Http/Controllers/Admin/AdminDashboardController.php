<?php

namespace App\Http\Controllers\Admin;

use App\AttendanceSetting;
use App\Currency;
use App\DashboardWidget;
use App\Helper\Reply;
use App\LeadFollowUp;
use App\Leave;
use App\Project;
use App\ProjectActivity;
use App\ProjectTimeLog;
use App\Task;
use App\TaskboardColumn;
use App\Ticket;
use App\Traits\CurrencyExchange;
use App\UserActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class AdminDashboardController extends AdminBaseController
{
    use CurrencyExchange;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.dashboard';
        $this->pageIcon = 'icon-speedometer';
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $taskBoardColumn = TaskboardColumn::all();

        $incompletedTaskColumn = $taskBoardColumn->filter(function ($value, $key) {
            return $value->slug == 'incomplete';
        })->first();

        $completedTaskColumn = $taskBoardColumn->filter(function ($value, $key) {
            return $value->slug == 'completed';
        })->first();


        $this->counts = DB::table('users')
            ->select(
                DB::raw('(select count(client_details.id) from `client_details` inner join role_user on role_user.user_id=client_details.user_id inner join users on client_details.user_id=users.id inner join roles on roles.id=role_user.role_id WHERE roles.name = "client" AND roles.company_id = ' . $this->user->company_id . ' AND client_details.company_id = ' . $this->user->company_id . ' and users.status = "active") as totalClients'),
                DB::raw('(select count(DISTINCT(users.id)) from `users` inner join role_user on role_user.user_id=users.id inner join roles on roles.id=role_user.role_id WHERE roles.name = "employee" AND users.company_id = ' . $this->user->company_id . ' and users.status = "active") as totalEmployees'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.company_id = ' . $this->user->company_id . ') as totalProjects'),
                DB::raw('(select count(invoices.id) from `invoices` where status = "unpaid" AND invoices.company_id = ' . $this->user->company_id . ') as totalUnpaidInvoices'),
                DB::raw('(select sum(project_time_logs.total_minutes) from `project_time_logs` WHERE project_time_logs.company_id = ' . $this->user->company_id . ') as totalHoursLogged'),
                DB::raw('(select count(tasks.id) from `tasks` where tasks.board_column_id=' . $completedTaskColumn->id . ' AND tasks.company_id = ' . $this->user->company_id . ') as totalCompletedTasks'),
                DB::raw('(select count(tasks.id) from `tasks` where tasks.board_column_id != ' . $completedTaskColumn->id . ' AND tasks.company_id = ' . $this->user->company_id . ') as totalPendingTasks'),
                DB::raw('(select count(attendances.id) from `attendances` inner join users as atd_user on atd_user.id=attendances.user_id where DATE(attendances.clock_in_time) = CURDATE()  AND attendances.company_id = ' . $this->user->company_id . ' and atd_user.status = "active") as totalTodayAttendance'),
                DB::raw('(select count(tickets.id) from `tickets` where (status="open" or status="pending") AND tickets.company_id = ' . $this->user->company_id . ') as totalUnResolvedTickets'),
                DB::raw('(select count(tickets.id) from `tickets` where (status="resolved" or status="closed") AND tickets.company_id = ' . $this->user->company_id . ') as totalResolvedTickets')
            )
            ->first();

        $timeLog = intdiv($this->counts->totalHoursLogged, 60) . ' ' . __('modules.hrs');

        if (($this->counts->totalHoursLogged % 60) > 0) {
            $timeLog .= ($this->counts->totalHoursLogged % 60) . ' ' . __('modules.mins');
        }

        $this->counts->totalHoursLogged = $timeLog;

        $this->pendingTasks = Task::with('project')
            ->where('tasks.board_column_id', '<>', $completedTaskColumn->id)
            ->where(DB::raw('DATE(due_date)'), '<=', Carbon::now()->timezone($this->global->timezone)->format('Y-m-d'))
            ->orderBy('due_date', 'desc')
            ->select('tasks.*')
            ->limit(15)
            ->get();

        $this->pendingLeadFollowUps = LeadFollowUp::with('lead')->where(DB::raw('DATE(next_follow_up_date)'), '<=', Carbon::now()->timezone($this->global->timezone)->format('Y-m-d'))
            ->join('leads', 'leads.id', 'lead_follow_up.lead_id')
            ->where('leads.next_follow_up', 'yes')
            ->where('leads.company_id', company()->id)
            ->get();

        $this->newTickets = Ticket::where('status', 'open')
            ->orderBy('id', 'desc')->get();

        $this->projectActivities = ProjectActivity::with('project')
            ->join('projects', 'projects.id', '=', 'project_activity.project_id')
            ->whereNull('projects.deleted_at')->select('project_activity.*')
            ->limit(15)->orderBy('id', 'desc')->get();
        $this->userActivities = UserActivity::with('user')->limit(15)->orderBy('id', 'desc')->get();

        $this->feedbacks = Project::with('client')->whereNotNull('feedback')->limit(5)->get();



        // earning chart
        // dd($this->languageSettings);
        $this->fromDate = Carbon::today()->timezone($this->global->timezone)->subDays(60);
        $this->toDate = Carbon::today()->timezone($this->global->timezone);
        $invoices = DB::table('payments')
            ->join('currencies', 'currencies.id', '=', 'payments.currency_id')
            ->whereDate('paid_on', '>=', $this->fromDate->format('Y-m-d'))
            ->whereDate('paid_on', '<=', $this->toDate->format('Y-m-d'))
            ->where('payments.status', 'complete')
            ->where('payments.company_id', company()->id)
            ->groupBy('paid_on')
            ->orderBy('paid_on', 'ASC')
            ->get([
                DB::raw('DATE_FORMAT(paid_on,"%Y-%m-%d") as date'),
                DB::raw('sum(amount) as total'),
                'currencies.currency_code',
                'currencies.is_cryptocurrency',
                'currencies.usd_price',
                'currencies.exchange_rate'
            ]);

        $chartData = array();
        foreach ($invoices as $chart) {
            if ($chart->currency_code != $this->global->currency->currency_code) {
                if ($chart->is_cryptocurrency == 'yes') {
                    if ($chart->exchange_rate == 0) {
                        if ($this->updateExchangeRates()) {
                            $usdTotal = ($chart->total * $chart->usd_price);
                            $chartData[] = ['date' => $chart->date, 'total' => floor($usdTotal / $chart->exchange_rate)];
                        }
                    } else {
                        $usdTotal = ($chart->total * $chart->usd_price);
                        $chartData[] = ['date' => $chart->date, 'total' => floor($usdTotal / $chart->exchange_rate)];
                    }
                } else {
                    if ($chart->exchange_rate == 0) {
                        if ($this->updateExchangeRates()) {
                            $chartData[] = ['date' => $chart->date, 'total' => floor($chart->total / $chart->exchange_rate)];
                        }
                    } else {
                        $chartData[] = ['date' => $chart->date, 'total' => floor($chart->total / $chart->exchange_rate)];
                    }
                }
            } else {
                $chartData[] = ['date' => $chart->date, 'total' => round($chart->total, 2)];
            }
        }

        $this->chartData = json_encode($chartData);
        $this->leaves = Leave::with('user', 'type')->where('status', '<>', 'rejected')
            ->whereYear('leave_date', Carbon::now()->timezone($this->global->timezone)->format('Y'))
            ->get();

        $this->widgets       = DashboardWidget::all();

        $this->activeWidgets = DashboardWidget::where('status', 1)->get()->pluck('widget_name')->toArray();

        if(DB::table('companies')->where('id',Auth::user()->company_id)->select('locale')->first()->locale=='vn'){
            $lang = 'vi';
        } else {
            $lang = 'en';
        }
        // dd($lang);
        Carbon::setLocale($lang);        
        return view('admin.dashboard.index', $this->data);
    }

    public function widget(Request $request)
    {
        $data = $request->all();
        unset($data['_token']);
        DashboardWidget::where('status', 1)->update(['status' => 0]);

        foreach ($data as $key => $widget) {
            DashboardWidget::where('widget_name', $key)->update(['status' => 1]);
        }

        return Reply::redirect(route('admin.dashboard'), __('messages.updatedSuccessfully'));
    }
}
