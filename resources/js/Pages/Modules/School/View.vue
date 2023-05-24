<template>
    <b-row>
        <b-col lg="12">
            <b-card no-body class="mt-n4 mx-n4">
                <div class="bg-soft-warning">
                    <b-card-body class="pb-0 px-4">
                        <b-row class="mb-3">
                            <b-col md>
                                <b-row class="align-items-center g-3">
                                    <b-col md="auto">
                                        <div class="avatar-md">
                                            <span v-if="school.data.avatar == 'school.jpg'"
                                                :class="'avatar-title rounded-circle bg-primary text-white fs-24'">{{school.data.name[0]}}</span>
                                            <img v-else :src="currentUrl+'/images/avatars/'+school.data.avatar" alt=""
                                                class="avatar-xs">
                                        </div>
                                    </b-col>
                                    <b-col md>
                                        <div>
                                            <h4 class="fw-bold">{{school.data.name}}</h4>
                                            <div class="hstack gap-3 flex-wrap">
                                                <div><i class="ri-building-line align-bottom me-1"></i>
                                                    {{ school.data.shortcut }}</div>
                                                <div class="vr"></div>
                                                <div><i class="ri-scales-fill align-bottom me-1"></i>
                                                    {{ school.data.class.name }}</div>
                                            </div>
                                        </div>
                                    </b-col>
                                </b-row>
                            </b-col>
                            <b-col md="auto">
                                <div class="hstack gap-1 flex-wrap">
                                    <!-- <button type="button" class="btn py-0 fs-16 favourite-btn active">
                                        <i class="ri-star-fill" @click="toggleFavourite"></i>
                                    </button> -->
                                    <Link href="/schools"><button type="button" class="btn py-0 fs-16 text-body">
                                        <i class="ri-share-line"></i>
                                    </button></Link>
                                    <!-- <button type="button" class="btn py-0 fs-16 text-body">
                                        <i class="ri-flag-line"></i>
                                    </button> -->
                                </div>
                            </b-col>
                        </b-row>

                        <ul class="nav nav-tabs-custom border-bottom-0" role="tablist">
                            <li class="nav-item">
                                <b-link class="nav-link active fw-semibold" data-bs-toggle="tab" href="#campuses" role="tab">
                                    Campuses
                                </b-link>
                            </li>
                        </ul>
                    </b-card-body>
                </div>
            </b-card>
        </b-col>
    </b-row>
    <b-row>
       <b-col lg="12">
            <div class="tab-content text-muted">
                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                    <b-row>
                        <b-col lg="12">
                            <b-card>
                                <b-card-body style="height: calc(100vh - 355px)">
                                    <b-row>
                                        <b-col lg>
                                            <div class="input-group mb-1">
                                                <span class="input-group-text"> <i class="ri-search-line search-icon"></i></span>
                                                <input type="text" v-model="keyword" placeholder="Search Campus" class="form-control" style="width: 40%;">
                                            </div>
                                        </b-col>
                                        <b-col lg="auto">
                                            <b-button class="me-1" type="button" variant="info" @click="create()">
                                                <i class="ri-filter-3-line align-bottom me-1"></i> Create
                                            </b-button>
                                        </b-col>
                                    </b-row>
                                    <b-row>
                                        <div class="table-responsive">
                                            <table class="table table-nowrap align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr class="fs-11">
                                                        <th style="width: 10%;">Campus</th>
                                                        <th style="width: 10%;" class="text-center">Main Campus</th>
                                                        <th style="width: 10%;" class="text-center">Term</th>
                                                        <th style="width: 10%;" class="text-center">Grading</th>
                                                        <th style="width: 15%;" class="text-center">Municipality</th>
                                                        <th style="width: 15%;" class="text-center">Province</th>
                                                        <th style="width: 15%;" class="text-center">Region</th>
                                                        <th style="width: 15%;" class="text-center">Assigned</th>
                                                        <th style="width: 10%;" class="text-center">Status</th>
                                                        <th style="width: 5%;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="list in school.data.campuses" v-bind:key="list.id" :class="[(list.is_active == 0) ? 'table-warnings' : '']">
                                                        <td v-b-tooltip.hover :title="list.oldname">{{list.campus}}</td>
                                                        <td class="text-center">
                                                            <span v-if="list.is_main" class="badge bg-success">Yes</span>
                                                            <span v-else class="badge bg-danger">No</span>
                                                        </td>
                                                        <td class="text-center">{{(list.term) ? list.term.name : ''}}</td>
                                                        <td class="text-center">{{(list.grading) ? list.grading.name : ''}}</td>
                                                        <td class="text-center">{{(list.municipality) ? list.municipality.name : ''}}</td>
                                                        <td class="text-center">{{(list.province) ? list.province.name : ''}}</td>
                                                        <td class="text-center">{{(list.region) ? list.region.name : ''}}</td>
                                                        <td class="text-center">{{(list.assigned) ? list.assigned.region : ''}}</td>
                                                        <td class="text-center">
                                                            <span v-if="list.is_active" class="badge bg-success">Active</span>
                                                            <span v-else class="badge bg-danger">Inactive</span>
                                                        </td>
                                                        <td class="text-end">
                                                            <b-button variant="soft-primary" v-b-tooltip.hover title="Edit" size="sm" class="edit-list me-1"><i class="ri-pencil-fill align-bottom"></i> </b-button>
                                                            <b-button @click="show(list)" variant="soft-info" v-b-tooltip.hover title="View" size="sm" class="edit-list"><i class="ri-eye-fill align-bottom"></i> </b-button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </b-row>
                                </b-card-body>
                            </b-card>
                        </b-col>
                    </b-row>
                </div>
            </div>
       </b-col>
    </b-row>
    <Campus :terms="terms" :gradings="gradings" :regions="regions" ref="campus"/>
    <Show ref="show"/>
</template>
<script>
import Campus from './Modals/Campus.vue';
import Show from './Modals/Show.vue';
export default {
    components : { Campus, Show },
    props: ['school','dropdowns','regions'],
    data() {
        return {

        }
    },
    computed: {
        terms : function() {
            return this.dropdowns.filter(x => x.classification === "Term Type");
        },
        classes : function() {
            return this.dropdowns.filter(x => x.classification === "Class");
        },
        gradings : function() {
            return this.dropdowns.filter(x => x.classification === "Grading System");
        },
        datares() {
            return this.$page.props.flash.datares;
        }
    },
    watch: {
        datares: {
            deep: true,
            handler(val = null) {
                if(val != null){
                    this.add(val.data);
                }
            },
        }
    },
    methods: {
        create(){
            this.$refs.campus.show(this.school.data.id);
        },
        add(data){
            this.school.data.campuses.unshift(data);
        },
        show(data){
            this.$refs.show.show(data);
        },
    }
}
</script>
