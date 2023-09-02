<template>
    <div>
        <div class="app-menu navbar-menu">
            <div class="navbar-brand-box">
                <Link href='/' class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="/images/sei.png" alt="" height="30" />
                    </span>
                    <span class="logo-lg">
                        <img src="/images/logo-dark.png" alt="" height="17" />
                    </span>
                </Link>
                <Link href="/" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="/images/sei.png" alt="" height="30" />
                    </span>
                    <span class="logo-lg">
                        <img src="/images/logo-light.png" alt="" height="17" />
                    </span>
                </Link>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover" @click="initActiveMenu">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>

             <ul class="navbar-nav" id="navbar-nav">
                <li class="nav-item">
                    <Link class="nav-link menu-link" :class="{'active': $page.component.startsWith('Modules/Home') }" href="/home">
                        <i class='ri ri-apps-line '></i>
                        <span data-key="t-dashboards">Home</span>
                    </Link>
                </li>
                <li class="nav-item" v-if="$page.props.auth.data.role == 'Administrator'">
                    <Link class="nav-link menu-link" :class="{'active': $page.component.startsWith('Modules/Staffs') }" href="/staffs">
                        <i class='bx bx-user-circle' ></i>
                        <span data-key="t-students">Staffs</span>
                    </Link>
                </li>    
                <li class="nav-item" v-if="$page.props.auth.data.role == 'Administrator'">
                    <Link class="nav-link menu-link" :class="{'active': $page.component.startsWith('Modules/Course') }" href="/courses">
                        <i class='bx bxs-graduation'></i>
                        <span data-key="t-students">Course</span>
                    </Link>
                </li>     
                <li class="nav-item" v-if="$page.props.auth.data.role == 'Administrator'">
                    <Link class="nav-link menu-link" :class="{'active': $page.component.startsWith('Modules/School') }" href="/schools">
                        <i class='bx bxs-school'></i>
                        <span data-key="t-students">Schools</span>
                    </Link>
                </li>   
                <li class="nav-item" v-if="$page.props.auth.data.role == 'Administrator'">
                    <Link class="nav-link menu-link" :class="{'active': $page.component.startsWith('Modules/Scholar') }" href="/scholars">
                        <i class='ri-team-fill'></i>
                        <span data-key="t-students">Scholars</span>
                    </Link>
                </li>  
                <li class="nav-item" v-if="$page.props.auth.data.role == 'Administrator'">
                    <Link class="nav-link menu-link" :class="{'active': $page.component.startsWith('Modules/Qualifier') }" href="/qualifiers">
                        <i class='ri-team-line'></i>
                        <span data-key="t-students">Qualifiers</span>
                    </Link>
                </li>  
                 <li class="nav-item" v-if="$page.props.auth.data.role == 'Staff'">
                    <Link class="nav-link menu-link" :class="{'active': $page.component.startsWith('Modules/School/Temp') }" href="/schools-temporary">
                        <i class='bx bxs-school'></i>
                        <span data-key="t-students">Schools Temporary</span>
                    </Link>
                </li>               
            </ul>
            
            
            <div class="sidebar-background"></div>
        </div>
        <div class="vertical-overlay" id="overlay"></div>
    </div>
</template>
<script>
export default {
    props: ['role']
}
</script>