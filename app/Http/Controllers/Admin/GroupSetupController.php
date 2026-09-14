<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GroupSetup;
use App\StaffSetup;
use DB;

class GroupSetupController extends Controller
{

    public function index(Request $request)
    {
        $title = "Group Setup";

        if ($this->userRole == 1) {
            $groups = GroupSetup::select('tbl_groups.*', 'tbl_staffs.name as teamLeaderName')
                ->join('tbl_staffs', 'tbl_staffs.id', '=', 'tbl_groups.team_leader')
                // ->where('tbl_groups.showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();
        } else {
            $groups = GroupSetup::select('tbl_groups.*', 'tbl_staffs.name as teamLeaderName')
                ->join('tbl_staffs', 'tbl_staffs.id', '=', 'tbl_groups.team_leader')
                // ->where('tbl_groups.showroom_id', $this->showroomId)
                ->where('tbl_groups.company_id', $this->company)
                ->orderBy('name', 'asc')
                ->get();
        }



        return view('admin.groupSetup.index')->with(compact('title', 'groups'));
    }

    public function add()
    {
        $title = "Add New Group";
        $formLink = "groupSetup.save";
        $buttonName = "Save";

        $groups = GroupSetup::select(DB::raw('group_concat(team_member) as team_member'))->where('company_id', $this->company)->first();
        $members = array($groups->team_member);
        $team_implode = implode(',', $members);
        $team_members = explode(',', $team_implode);

        $staffs = StaffSetup::where('status', '1')
            ->where('company_id', $this->company)
            // ->where('showroom_id', $this->showroomId)
            ->whereNotIn('id', $team_members)
            ->orderBy('id', 'asc')
            ->get();

        return view('admin.groupSetup.add')->with(compact('title', 'formLink', 'buttonName', 'staffs'));
    }

    public function save(Request $request)
    {

        $this->validate(request(), [
            'name' => 'required|unique:tbl_groups',
            'teamLeader' => 'required|unique:tbl_groups,team_leader',
        ]);

        if ($request->teamMember) {
            $teamMember = implode(',', $request->teamMember);
        } else {
            $teamMember = "";
        }

        GroupSetup::create([
            'company_id' => $this->company,
            'showroom_id' => $this->showroomId,
            'name' => $request->name,
            'team_leader' => $request->teamLeader,
            'team_member' => $teamMember,
            'status' => 1,
            'created_by' => $this->userId
        ]);

        return redirect(route('groupSetup.index'))->with('msg', 'Group Successfuly Saved');
    }

    public function edit($id)
    {
        $title = "Edit Group";
        $formLink = "groupSetup.update";
        $buttonName = "Update";

        $group = GroupSetup::where('id', $id)->first();

        $groups = GroupSetup::select(DB::raw('group_concat(team_member) as team_member'))
            ->where('company_id', $this->company)
            ->whereNotIn('id', [$group->id])
            ->first();


        $members = array($groups->team_member);
        $team_implode = implode(',', $members);
        $team_members = explode(',', $team_implode);

        // dd($groups, $this->company);
        
        $staffs = StaffSetup::where('status', '1')
            ->where('company_id', $this->company)
            ->whereNotIn('id', $team_members)
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.groupSetup.edit')->with(compact('title', 'formLink', 'buttonName', 'staffs', 'group'));
    }

    public function update(Request $request)
    {
        $groupId = $request->groupId;

        $this->validate(request(), [
            'name' => 'required|unique:tbl_groups,name,' . $groupId,
            'teamLeader' => 'required|unique:tbl_groups,team_leader,' . $groupId,
        ]);

        if ($request->teamMember) {
            $teamMember = implode(',', $request->teamMember);
        } else {
            $teamMember = "";
        }

        $group = GroupSetup::find($groupId);

        $group->update([
            // 'showroom_id' => $this->showroomId,
            'name' => $request->name,
            'team_leader' => $request->teamLeader,
            'team_member' => $teamMember,
            'updated_by' => $this->userId
        ]);

        return redirect(route('groupSetup.index'))->with('msg', 'Group Successfully Updated');
    }

    public function getAllStaff(Request $request)
    {
        $output = '';
        $results = '';
        $staffId = $request->staffId;

        $teamMembers = StaffSetup::select('tbl_staffs.*')
            ->where('showroom_id', $this->showroomId)
            ->orderBy('name', 'asc')
            ->get();

        if ($teamMembers) {
            $output .= '<select class="form-control chosen-select" data-placeholder="Select Team Members" id="teamMember" name="teamMember[]" multiple>';
            foreach ($teamMembers as $teamMember) {
                $output .= '<option value="' . $teamMember->id . '">' . $teamMember->name . '</option>';
            }
            $output .= '</select>';
        } else {
            $output .= '<select class="form-control chosen-select" data-placeholder="Select Team Members" id="teamMember" name="teamMember[]" multiple>';
            $output .= '</select>';
        }

        echo $output;
    }

    public function delete(Request $request)
    {
        GroupSetup::where('id', $request->groupId)->delete();
    }

    public function changeStatus(Request $request)
    {
        $groupId = $request->groupId;

        $group = GroupSetup::find($groupId);

        if ($group->status == 1) {
            $group->update([
                'status' => 0
            ]);
        } else {
            $group->update([
                'status' => 1
            ]);
        }
    }
}
