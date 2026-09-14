@php
use Illuminate\Support\Facades\URL;
$userId = Auth::user()->id;
@endphp

<nav class="navbar top-navbar navbar-expand-md navbar-dark">
    <div class="navbar-header">
        <a class="navbar-brand logo-link" href="{{ url('/admin') }}">
            <span class="small-logo">
                <img src="{{ asset('public/uploads/admin_logo/small_logo.png') }}" alt="mini" />
            </span>

            <span class="logo-lg">
                <img src="{{ asset('public/uploads/images/2019_10_22/logo-2.png') }}" class="light-logo large-logo"
                     alt="large" />
            </span>
        </a>
    </div>
    <!-- End Logo -->

    <div class="navbar-collapse">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link nav-toggler d-block d-sm-none waves-effect waves-dark" href="javascript:void(0)">
                    <i class="ti-menu"></i>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link sidebartoggler d-none d-lg-block d-md-block waves-effect waves-dark"
                   href="javascript:void(0)">
                    <i class="icon-menu"></i>
                </a>
            </li>
        </ul>

        <div class="showroom-view">
            @php
            $company = \App\CompanySetup::find(auth()->user()->company_id);

            $showroomId = Session::get('showroom');
            $showroomName = DB::table('tbl_showroom')
            ->where('id', $showroomId)
            ->first();
            @endphp

            @if ($company)
            {{ @$company->name }}
            @endif
            @if ($showroomName != '')
            / {{ @$showroomName->name }}
            @endif
        </div>

        <ul class="navbar-nav my-lg-0">
            <li class="nav-item dropdown u-pro">
                <a class="nav-link dropdown-toggle waves-effect waves-dark profile-pic" href="" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false"><img
                        src="{{ asset('/public/admin-elite/assets/images/users/1.jpg') }}" alt="user"
                        class="">
                    <span class="hidden-md-down text-white">{{ Auth::user() ? Auth::user()->name : 'Admin' }}
                        <i class="fa fa-angle-down"></i>
                    </span>
                </a>

                <div class="dropdown-menu dropdown-menu-right animated flipInY">
                    <a href="javascript:;" class="dropdown-item userForm">
                        <form action="{{ route('user.profile') }}" method="post" id="userForm">
                            {{ csrf_field() }}
                            <input type="hidden" name="userId" value="{{ $userId }}">
                            <i class="ti-user"></i> My Profile
                        </form>
                    </a>
                    <div class="dropdown-divider"></div>
                    @if (auth()->user()->role == 1)
                    <a href="{{ route('user.changePassword', $userId) }}" class="dropdown-item">
                        <i class="fa fa-exchange text-inverse m-r-10"></i> Change Password
                    </a>
                    @else
                    <a href="{{ route('user.changePass', $userId) }}" class="dropdown-item">
                        <i class="fa fa-exchange text-inverse m-r-10"></i> Change Password
                    </a>
                    @endif
                    <div class="dropdown-divider"></div>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>

                    <a href="{{ Auth::guard('admin')->check() ? route('admin.logout') : (Auth::guard('web')->check() ? route('user.logout') : (Auth::guard('customer')->check() ? route('customer.logout') : route('logout'))) }}"
                       class="dropdown-item"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa fa-power-off"></i> Logout
                    </a>
                </div>
            </li>

<!--            <li class="nav-item right-side-toggle">
                <a class="nav-link  waves-effect waves-light" href="javascript:void(0)">
                    <i class="ti-settings"></i>
                </a>
            </li>-->
        </ul>
    </div>
</nav>

<script>
    const myForm = document.getElementById("userForm");
    document.querySelector(".userForm").addEventListener("click", function () {
        myForm.submit();
    });
</script>

<style>
    .topbar .top-navbar .navbar-header {
        background-color: #fff !important;
    }
    .topbar{
        background-color: #49afbd !important;
    }
</style>