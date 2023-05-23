<template>
    <div class="profile-foreground position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg">
            <img src="/assets/images/profile-bg.jpg" alt="" class="profile-wid-img" />
        </div>
    </div>
    <div class="pt-4 mb-4 mb-lg-3 pb-lg-4">
        <b-row class="g-4">
            <b-col cols="auto">
                <div class="avatar-lg">
                    <img :src="currentUrl+'/images/avatars/'+scholar.profile.avatar" alt="scholar-img" class="img-thumbnail rounded-circle" />
                </div>
            </b-col>
            <b-col>
                <div class="p-2">
                    <h3 class="text-white mb-1">{{scholar.profile.lastname}}, {{scholar.profile.firstname}} {{scholar.profile.middlename[0]}}.</h3>
                    <h6 class="text-white-75 fw-bold"><span class="badge bg-info">{{scholar.status.name}}</span></h6>
                    <div class="hstack text-white-50 gap-1 mt-3">
                        <div class="me-3">
                            <i class="ri-map-pin-fill me-2 text-white-75 fs-16 align-middle"></i>  
                            <span v-if="scholar.address.province != undefined">{{scholar.address.name }}</span>
                            <span v-else>{{scholar.address.name.toUpperCase() }}</span>
                        </div>
                        <div class="me-3">
                            <i class="ri-building-line me-1 text-white-75 fs-16 align-middle"></i>
                            {{ (!Object.keys(scholar.education.school).includes('name'))  ? scholar.education.school : scholar.education.school.name }} {{(scholar.education.school.campus != 'Main') ? ' - '+scholar.education.school.campus : ''}}
                        </div>
                        <div class="me-3">
                            <i class="mdi mdi-school-outline me-1 text-white-75 fs-16 align-middle"></i>
                            {{ (!Object.keys(scholar.education.course).includes('name'))  ? scholar.education.course : scholar.education.course.name }}
                        </div>
                    </div>
                </div>
            </b-col>
            <b-col cols="12" lg="auto" order-lg="0" class="order-last">
                <b-row class="text text-white-50 text-center">
                    <b-col lg="12">
                        <div class="p-2">
                            <h4 class="text-white mb-1">{{scholar.spas_id}}</h4>
                            <p class="fs-14 mb-0 fw-bold">SPAS ID</p>
                        </div>
                    </b-col>
                </b-row>
            </b-col>
        </b-row>
    </div>

    <b-row>
        <b-col lg="12" class="mt-n2">
            <div>
                <div class="d-flex">
                    <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" role="tablist">
                        <li class="nav-item">
                            <b-link class="nav-link fs-12 active" data-bs-toggle="tab" href="#overview-tab" role="tab">
                                <i class="ri-airplay-fill d-inline-block d-md-none"></i>
                                <span class="d-none d-md-inline-block">Overview</span>
                            </b-link>
                        </li>
                        <li class="nav-item">
                            <b-link class="nav-link fs-12" data-bs-toggle="tab" href="#financial" role="tab">
                                <i class="ri-list-unordered d-inline-block d-md-none"></i>
                                <span class="d-none d-md-inline-block">Financial Benefits</span>
                            </b-link>
                        </li>
                        <li class="nav-item">
                            <b-link class="nav-link fs-12" data-bs-toggle="tab" href="#enrollment" role="tab">
                                <i class="ri-price-tag-line d-inline-block d-md-none"></i>
                                <span class="d-none d-md-inline-block">Enrollments</span>
                            </b-link>
                        </li>
                        <li class="nav-item">
                            <b-link class="nav-link fs-12" data-bs-toggle="tab" href="#history" role="tab">
                                <i class="ri-folder-4-line d-inline-block d-md-none"></i>
                                <span class="d-none d-md-inline-block">Employment History</span>
                            </b-link>
                        </li>
                        <li class="nav-item">
                            <b-link class="nav-link fs-12" data-bs-toggle="tab" href="#prospectus" role="tab">
                                <i class="ri-folder-4-line d-inline-block d-md-none"></i>
                                <span class="d-none d-md-inline-block">Prospectus</span>
                            </b-link>
                        </li>
                    </ul>
                    <div class="flex-shrink-0">
                        <router-link to="/pages/profile-setting" class="btn btn-success"><i
                                class="ri-edit-box-line align-bottom"></i> Edit Profile</router-link>
                    </div>
                </div>
                <div class="tab-content pt-4 text-muted">
                    
                    <div class="tab-pane active" id="overview-tab" role="tabpanel"> 
                        <Overview />
                    </div>
                    <div class="tab-pane" id="financial" role="tabpanel"> 
                        <Financial :id="scholar.id" :ays="scholar.ays" :types="scholar.types" :privileges="privileges" :benefits="benefits"/>
                    </div>
                    <div class="tab-pane" id="enrollment" role="tabpanel"> 
                        <Enrollment :enrollments="scholar.enrollments"/>
                    </div>
                    <div class="tab-pane" id="history" role="tabpanel"> 
                        <History />
                    </div>
                    <div class="tab-pane" id="prospectus" role="tabpanel"> 
                        <Prospectus :prospectus="scholar.education.information.prospectus"/>
                    </div>
                </div>
            </div>
        </b-col>
    </b-row>
</template>

<script>
import Grade from './Grade.vue';
import History from './History.vue';
import Prospectus from './Prospectus.vue';
import Overview from './Overview.vue';
import Financial from './Financial.vue';
import Enrollment from './Enrollment.vue';
export default {
    components : { Overview, Financial, Enrollment, Grade, History, Prospectus },
    props: ['user','privileges','benefits'],
    data(){
        return {
            currentUrl: window.location.origin,
            scholar: {}
        }
    },
    created(){
        this.scholar = this.user.data;
    }
}
</script>
