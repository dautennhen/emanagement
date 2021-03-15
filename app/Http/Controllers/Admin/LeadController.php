<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\LeadsDataTable;
use App\Helper\Reply;
use App\Http\Requests\CommonRequest;
use App\Http\Requests\Gdpr\SaveConsentLeadDataRequest;
use App\Http\Requests\Lead\StoreRequest;
use App\Http\Requests\Lead\UpdateRequest;
use App\Lead;
use App\LeadAgent;
use App\LeadFollowUp;
use App\LeadSource;
use App\LeadStatus;
use App\PurposeConsent;
use App\PurposeConsentLead;
use Carbon\Carbon;
use DebugBar\DebugBar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class LeadController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageIcon = __('icon-people');
        $this->pageTitle = 'app.menu.lead';
        // $this->pageTitle = "kjhkjhk";
        $this->middleware(function ($request, $next) {
            if (!in_array('leads', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index(LeadsDataTable $dataTable)
    {
        $this->totalLeads = Lead::all();

        $this->totalClientConverted = $this->totalLeads->filter(function ($value, $key) {
            return $value->client_id != null;
        });
        $this->totalClientConverted = $this->totalClientConverted->count();
        $this->totalLeads = $this->totalLeads->count() - $this->totalClientConverted;

        $this->pendingLeadFollowUps = LeadFollowUp::where(\DB::raw('DATE(next_follow_up_date)'), '<=', Carbon::today()->format('Y-m-d'))
            ->join('leads', 'leads.id', 'lead_follow_up.lead_id')
            ->where('leads.next_follow_up', 'yes')
            ->where('leads.company_id', company()->id) //get current company
            ->get();
        $this->pendingLeadFollowUps = $this->pendingLeadFollowUps->count();
        $this->leadAgents = LeadAgent::with('user')->get();
//        $this->status=LeadStatus::join('companies','companies.id','=','lead_status.company_id')->get();
    //    dd($this->data);
        return $dataTable->render('admin.lead.index', $this->data);
    }

    public function show($id)
    {
        $this->lead = Lead::findOrFail($id);
        return view('admin.lead.show', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->leadAgents = LeadAgent::with('user')->get();
        $this->sources = LeadSource::all();
        $this->status = LeadStatus::all();
        return view('admin.lead.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $lead = new Lead();
        $lead->company_name = $request->company_name;
        $lead->website = $request->website;
        $lead->address = $request->address;
        $lead->client_name = $request->client_name;
        $lead->client_email = $request->client_email;
        $lead->mobile = $request->mobile;
        $lead->note = $request->note;
        $lead->next_follow_up = $request->next_follow_up;
        $lead->agent_id = $request->agent_id;
        $lead->source_id = $request->source_id;
        
        //default status 'pending'
        $lead->status_id = LeadStatus::where('type','=','pending')->first()->id;
        $lead->save();

        //log search
        $this->logSearchEntry($lead->id, $lead->client_name, 'admin.leads.show', 'lead');
        $this->logSearchEntry($lead->id, $lead->client_email, 'admin.leads.show', 'lead');
        if (!is_null($lead->company_name)) {
            $this->logSearchEntry($lead->id, $lead->company_name, 'admin.leads.show', 'lead');
        }

        return Reply::redirect(route('admin.leads.index'), __('messages.LeadAddedUpdated'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->leadAgents = LeadAgent::with('user')->get();
        $this->lead = Lead::findOrFail($id);
        $this->sources = LeadSource::all();
        $this->status = LeadStatus::all();
        return view('admin.lead.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        $lead = Lead::findOrFail($id);
        $lead->company_name = $request->company_name;
        $lead->website = $request->website;
        $lead->address = $request->address;
        $lead->client_name = $request->client_name;
        $lead->client_email = $request->client_email;
        $lead->mobile = $request->mobile;
        $lead->note = $request->note;
        $lead->status_id = $request->status;
        $lead->source_id = $request->source;
        $lead->next_follow_up = $request->next_follow_up;
        $lead->agent_id = $request->agent_id;

        $lead->save();

        return Reply::redirect(route('admin.leads.index'), __('messages.LeadUpdated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Lead::destroy($id);
        return Reply::success(__('messages.LeadDeleted'));
    }

    /**
     * @param $id
     * @return array
     */
    public function deleteFollow($id)
    {
        LeadFollowUp::destroy($id);
        return Reply::success(__('messages.followUp.deletedSuccess'));
    }

    /**
     * @param CommonRequest $request
     * @return array
     */
    public function changeStatus(CommonRequest $request)
    {
        $lead = Lead::findOrFail($request->leadID);
            $lead->status_id = $request->statusID;
        $lead->save();

        return Reply::success(__('messages.leadStatusChangeSuccess'));
    }

    public function gdpr($leadID)
    {
        $this->lead = Lead::findOrFail($leadID);
        $this->allConsents = PurposeConsent::with(['lead' => function ($query) use ($leadID) {
            $query->where('lead_id', $leadID)
                ->orderBy('created_at', 'desc');
        }])->get();

        return view('admin.lead.gdpr.show', $this->data);
    }

    public function consentPurposeData($id)
    {
        $purpose = PurposeConsentLead::select('purpose_consent.name', 'purpose_consent_leads.created_at', 'purpose_consent_leads.status', 'purpose_consent_leads.ip', 'users.name as username', 'purpose_consent_leads.additional_description')
            ->join('purpose_consent', 'purpose_consent.id', '=', 'purpose_consent_leads.purpose_consent_id')
            ->leftJoin('users', 'purpose_consent_leads.updated_by_id', '=', 'users.id')
            ->where('purpose_consent_leads.lead_id', $id);

        return DataTables::of($purpose)
            ->editColumn('status', function ($row) {
                if ($row->status == 'agree') {
                    $status = __('modules.gdpr.optIn');
                } else if ($row->status == 'disagree') {
                    $status = __('modules.gdpr.optOut');
                } else {
                    $status = '';
                }

                return $status;
            })
            ->make(true);
    }

    public function saveConsentLeadData(SaveConsentLeadDataRequest $request, $id)
    {
        $lead = Lead::findOrFail($id);
        $consent = PurposeConsent::findOrFail($request->consent_id);

        if ($request->consent_description && $request->consent_description != '') {
            $consent->description = $request->consent_description;
            $consent->save();
        }

        // Saving Consent Data
        $newConsentLead = new PurposeConsentLead();
        $newConsentLead->lead_id = $lead->id;
        $newConsentLead->purpose_consent_id = $consent->id;
        $newConsentLead->status = trim($request->status);
        $newConsentLead->ip = $request->ip();
        $newConsentLead->updated_by_id = $this->user->id;
        $newConsentLead->additional_description = $request->additional_description;
        $newConsentLead->save();

        $url = route('admin.leads.gdpr', $lead->id);

        return Reply::redirect($url);
    }


    /**
     * @param $leadID
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function followUpCreate($leadID)
    {
        $this->leadID = $leadID;
        return view('admin.lead.follow_up', $this->data);
    }

    /**
     * @param CommonRequest $request
     * @return array
     */
    public function followUpStore(\App\Http\Requests\FollowUp\StoreRequest $request)
    {
        $followUp = new LeadFollowUp();
        $followUp->lead_id = $request->lead_id;
        $followUp->next_follow_up_date = Carbon::createFromFormat($this->global->date_format, $request->next_follow_up_date)->format('Y-m-d');
        $followUp->remark = $request->remark;
        
        
        $followUp->save();
       
        // $this->follow=LeadFollowUp::orderBy('id','desc')->get();
        $this->lead = Lead::findOrFail($request->lead_id);
        $this->lead->follow =LeadFollowUp::where('lead_id',$request->lead_id)->orderBy('id', 'desc')->get();
        $view = view('admin.lead.followup.task-list-ajax', $this->data)->render();
        
        

        return Reply::successWithData(__('messages.leadFollowUpAddedSuccess'), ['html' => $view]);
    }

    public function followUpShow($leadID)
    {
        
        $this->leadID = $leadID;
        $this->lead = Lead::findOrFail($leadID);
        return view('admin.lead.followup.show', $this->data);
    }

    public function editFollow($id)
    {
        $this->follow = LeadFollowUp::findOrFail($id);
        $view = view('admin.lead.followup.edit', $this->data)->render();
        return Reply::dataOnly(['html' => $view]);
    }

    public function UpdateFollow(\App\Http\Requests\FollowUp\StoreRequest $request)
    {
        $followUp = LeadFollowUp::findOrFail($request->id);
        $followUp->lead_id = $request->lead_id;

        $followUp->next_follow_up_date = Carbon::createFromFormat($this->global->date_format, $request->next_follow_up_date)->format('Y-m-d');
        $followUp->remark = $request->remark;
        $followUp->save();

        $this->lead = Lead::findOrFail($request->lead_id);

        $view = view('admin.lead.followup.task-list-ajax', $this->data)->render();

        return Reply::successWithData(__('messages.leadFollowUpUpdatedSuccess'), ['html' => $view]);
    }

    public function followUpSort(CommonRequest $request)
    {
        $leadId = $request->leadId;
        $this->sortBy = $request->sortBy;

        $this->lead = Lead::findOrFail($leadId);
        if ($request->sortBy == 'next_follow_up_date') {
            $order = "asc";
        } else if($request->sortBy == 'desc')  {
            $request->sortBy ="next_follow_up_date";
            $order = "desc";
        }else{
            $order = "desc";
        }


        $follow = LeadFollowUp::where('lead_id', $leadId)->orderBy($request->sortBy, $order);


        $this->lead->follow = $follow->get();

        $view = view('admin.lead.followup.task-list-ajax', $this->data)->render();

        return Reply::successWithData(__('messages.followUpFilter'), ['html' => $view]);
    }

    public function kanbanboard(Request $request)
    {
        $this->startDate = $startDate = Carbon::now()->subDays(15)->format($this->global->date_format);
        $this->endDate = $endDate = Carbon::now()->addDays(15)->format($this->global->date_format);
        $this->leadAgents = LeadAgent::with('user')->get();

        if (request()->ajax()) {

            $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate)->toDateString();
            $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->toDateString();

            $boardColumns = LeadStatus::with(['leads' => function ($q) use ($startDate, $endDate, $request) {
                $q->with(['lead_agent', 'lead_agent.user'])
                    ->select('leads.*',  \DB::raw("(select next_follow_up_date from lead_follow_up where lead_id = leads.id and leads.next_follow_up  = 'yes' ORDER BY next_follow_up_date asc limit 1) as next_follow_up_date"))
                    ->groupBy('leads.id');

                $q->where(function ($task) use ($startDate, $endDate) {
                    $task->whereBetween(DB::raw('DATE(leads.`created_at`)'), [$startDate, $endDate]);

                    $task->orWhereBetween(DB::raw('DATE(leads.`created_at`)'), [$startDate, $endDate]);
                });

                if ($request->assignedTo != '' && $request->assignedTo !=  null && $request->assignedTo !=  'all') {
                    $q->where('leads.agent_id', '=', $request->assignedTo);
                }
            }])->orderBy('priority', 'asc')->get();

            $this->boardColumns = $boardColumns;

            $this->startDate = $startDate;
            $this->endDate = $endDate;

            $view = view('admin.lead.board_data', $this->data)->render();
            return Reply::dataOnly(['view' => $view]);
        }
        return view('admin.lead.kanban_board', $this->data);
    }

    public function updateIndex(Request $request)
    {

        $taskIds = $request->taskIds;
        $boardColumnIds = $request->boardColumnIds;
        $priorities = $request->prioritys;

        $board = LeadStatus::findOrFail($boardColumnIds[0]);

        if (isset($taskIds) && count($taskIds) > 0) {

            $taskIds = (array_filter($taskIds, function ($value) {
                return $value !== null;
            }));

            foreach ($taskIds as $key => $taskId) {
                if (!is_null($taskId)) {
                    $task = Lead::findOrFail($taskId);
                    $task->update(
                        [
                            'status_id' => $boardColumnIds[$key],
                            'column_priority' => $priorities[$key]
                        ]
                    );
                }
            }

            if ($request->draggingTaskId == 0 && $request->draggedTaskId != 0) {
                // $this->logTaskActivity($request->draggedTaskId, $this->user->id, "statusActivity", $board->id);
                // $updatedTask = Task::findOrFail($request->draggedTaskId);
                // event(new TaskUpdated($updatedTask));
            }
        }



        return Reply::dataOnly(['status' => 'success']);
    }
}
