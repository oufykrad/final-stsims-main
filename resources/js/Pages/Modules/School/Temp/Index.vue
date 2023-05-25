<template>
    <Head title="Dashboard" />
    <b-row>
        <b-col lg="12">
            <b-card no-body class="mt-n4 mx-n4">
                <div class="bg-soft-warning">
                    <b-card-body class="pb-0 px-4">
                        <b-row class="mb-2">
                            <b-col md>
                                <b-row class="align-items-center g-3">
                                    <b-col md="auto">
                                        <div class="avatar-md">
                                            <span v-if="agency.avatar == 'avatar.jpg'"
                                                :class="'avatar-title rounded-circle bg-primary text-white fs-24'">{{agency.name[0]}}</span>
                                            <img v-else :src="currentUrl+'/images/avatars/'+agency.avatar" alt=""
                                                class="avatar-xs">
                                        </div>
                                    </b-col>
                                    <b-col md>
                                        <div>
                                            <h4 class="fw-bold">{{agency.name}}</h4>
                                            <div class="hstack gap-3 flex-wrap">
                                                <div><i class="ri-building-line align-bottom me-1"></i>
                                                    {{ agency.acronym }}</div>
                                                <div class="vr"></div>
                                                <div><i class="ri-map-pin-fill align-bottom me-1"></i>
                                                    {{ agency.region.name }},  {{ agency.region.region }} </div>
                                            </div>
                                        </div>
                                    </b-col>
                                </b-row>
                            </b-col>
                        </b-row>

                        <ul class="nav nav-tabs-custom border-bottom-0" role="tablist">
                            <li class="nav-item">
                                <b-link class="nav-link active fw-semibold" data-bs-toggle="tab" href="#campuses" role="tab">
                                    Schools
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
                                <b-card-body style="height: calc(100vh - 350px)">
                                    <b-row>
                                        <b-col lg>
                                            <div class="input-group mb-1">
                                                <span class="input-group-text"> <i class="ri-search-line search-icon"></i></span>
                                                <input type="text" v-model="keyword" placeholder="Search Campus" class="form-control" style="width: 40%;">
                                                <b-button @click="create()" type="button" variant="primary">
                                                    <i class="ri-add-circle-fill align-bottom me-1"></i> Add School
                                                </b-button>
                                            </div>
                                        </b-col>
                                    </b-row>
                                    <b-row>
                                        <div class="table-responsive mt-2">
                                            <table class="table table-nowrap align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr class="fs-11">
                                                        <th style="width: 10%;">Name</th>
                                                        <th style="width: 10%;" class="text-center">Main Campus</th>
                                                        <th style="width: 10%;" class="text-center">Term</th>
                                                        <th style="width: 10%;" class="text-center">Grading</th>
                                                        <th style="width: 15%;" class="text-center">Municipality</th>
                                                        <th style="width: 15%;" class="text-center">Province</th>
                                                        <th style="width: 15%;" class="text-center">Region</th>
                                                        <th style="width: 15%;" class="text-center">Assigned</th>
                                                        <th style="width: 10%;" class="text-center">Status</th>
                                                        <!-- <th style="width: 5%;"></th> -->
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="list in lists" v-bind:key="list.id" class="fs-11" :class="[(list.is_active == 0) ? 'table-warnings' : '']">
                                                        <td v-b-tooltip.hover :title="list.oldname">{{list.name}} - {{list.campus}}</td>
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
                                                        <!-- <td class="text-end">
                                                            <b-button variant="soft-primary" v-b-tooltip.hover title="Edit" size="sm" class="edit-list me-1"><i class="ri-pencil-fill align-bottom"></i> </b-button>
                                                            <b-button @click="show(list)" variant="soft-info" v-b-tooltip.hover title="View" size="sm" class="edit-list"><i class="ri-eye-fill align-bottom"></i> </b-button>
                                                        </td> -->
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <Pagination class="ms-2 me-2" v-if="meta" @fetch="fetch" :lists="lists.length" :links="links" :pagination="meta" />
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
    <Create @info="fetch()" :agency_code="agency.region.code" :classes="classes" :terms="terms" :gradings="gradings" :regions="regions" ref="create"/>
</template>
<script>
import Create from './Modals/Create.vue';
import PageHeader from "@/Shared/Components/PageHeader.vue";
import Pagination from "@/Shared/Components/Pagination.vue";
export default {
    props: ['agency','dropdowns', 'regions'],
    components: { PageHeader, Pagination, Create },
    data() {
        return {
            currentUrl: window.location.origin,
            lists: [],
            meta: {},
            links: {},
            keyword: null
        };
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
    created(){
        this.fetch();
    },
    methods: {
        create(){
            this.$refs.create.show();
        },
         fetch(page_url) {
            page_url = page_url || '/schools-temporary';
            axios.get(page_url, {
                params: {
                    keyword: this.keyword,
                    region: (this.region) ? this.region.code : '',
                    province: (this.province) ? this.province.code : '',
                    municipality: (this.municipality) ? this.municipality.code : '',
                    counts: ((window.innerHeight-410)/56),
                    type: 'lists'
                }
            })
            .then(response => {
                this.lists = response.data.data;
                this.meta = response.data.meta;
                this.links = response.data.links;
            })
            .catch(err => console.log(err));
        },
    }
}
</script>
