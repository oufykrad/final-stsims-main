<template>
<Head title="Students" />
<PageHeader :title="title" :items="items" />
    <div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1">

        <div class="file-manager-sidebar">
            <History />
        </div>
            
        <div class="file-manager-content w-100 p-4 pb-0" style="height: calc(100vh - 180px)">
            
            <div class="p-3 bg-light rounded mb-4">
                <b-row class="g-2">
                    <b-col lg>
                        <div class="search-box">
                            <input type="text" id="searchTaskList" class="form-control search"
                                placeholder="Search name">
                            <i class="ri-search-line search-icon"></i>
                        </div>
                    </b-col>
                    <b-col lg="auto">
                        <b-button variant="primary" class="createTask" @click="add"> <i
                                class="ri-add-fill align-bottom"></i> New Staff
                        </b-button>
                    </b-col>
                </b-row>
            </div>

            <div class="todo-content position-relative px-4 mx-n4" id="todo-content">
                <div id="elmLoader" v-if="lists.length == 0">
                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                        colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px"></lord-icon>
                    <h5 class="mt-4">Sorry! No Result Found</h5>
                </div>
                <div class="todo-task" id="todo-task" v-else>
                    <div class="table-responsive">
                        <table class="table align-middle position-relative table-nowrap">
                            <thead class="bg-soft-light">
                                <tr>
                                    <th width="2%"></th>
                                    <th width="20%">Name</th>
                                    <th width="30%" class="text-center">Agency</th>
                                    <th width="15%" class="text-center">Role</th>
                                    <th width="15%" class="text-center">Status</th>
                                    <th width="10%"></th>
                                </tr>
                            </thead>

                            <tbody id="task-lists">
                                <tr v-for="(list, index) of lists" :key="index">
                                    <td>
                                        <div class="avatar-xs">
                                            <span v-if="list.avatar == 'avatar.jpg'" :class="'avatar-title rounded-circle bg-'+list.g+' text-white'">{{list.lastname[0]}}</span>
                                            <img v-else :src="currentUrl+'/images/avatars/'+list.avatar" alt="" class="avatar-xs rounded-circle">
                                        </div>
                                        
                                    </td>
                                    <td class="fs-14 my-1 fw-medium">{{ list.name}}</td>
                                     <td class="text-center">{{ (list.agency) ? list.agency.acronym : 'not available' }}</td>
                                    <td class="text-center">{{ list.role }}</td>
                                    <td class="text-center">
                                        <span v-if="list.is_active == 1" class="badge bg-success fs-10">Active</span>
                                        <span v-else class="badge bg-danger fs-10">Inactive</span>
                                    </td>
                                    <td class="text-end">
                                        <b-button variant="soft-success" @click="update('status',list,index)" v-b-tooltip.hover title="Lock" size="sm" class="remove-list me-1"><i class="ri-lock-2-fill align-bottom"></i></b-button>
                                        <b-button variant="soft-warning"  @click="update('verify',list,index)" v-b-tooltip.hover title="Verify" size="sm" class="remove-list me-1"><i class="ri-mail-send-fill align-bottom"></i></b-button>
                                        <b-button variant="soft-danger" @click="view(list)" v-b-tooltip.hover title="View" size="sm" class="remove-list me-1"><i class="ri-eye-fill align-bottom"></i></b-button>
                                        <b-button variant="soft-primary" v-b-tooltip.hover title="Edit" size="sm" class="edit-list"><i class="ri-pencil-fill align-bottom"></i> </b-button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="py-4 mt-4 text-center" id="noresult" style="display: none;">
                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                        colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px"></lord-icon>
                    <h5 class="mt-4">Sorry! No Result Found</h5>
                </div>
            </div>
        </div>
    </div>
<Add :agencies="agencies" ref="new"/>
<Update ref="update"/>
<View ref="view"/>
</template>
<script>
import History from './History.vue';
import Lists from './Lists.vue';
import Add from './Modals/Add.vue';
import Update from './News/Update.vue';
import View from './News/View.vue';
import Multiselect from '@suadelabs/vue3-multiselect';
import PageHeader from "@/Shared/Components/PageHeader.vue";
import flatPickr from "vue-flatpickr-component";
export default {
    components : { PageHeader, Multiselect, flatPickr, Add, Update, Lists, History, View },
    props: ['agencies'],
    page: {
        title: "List of Staffs",
        meta: [{ name: "description" }],
    },
    data() {
        return {
            currentUrl: window.location.origin,
            title: "List of Staffs",
            items: [{text: "Lists",href: "/",},{text: "Staff",active: true,},],
            lists : [],
            meta: {},
            links: {},
            index: '',
        };
    },
    created(){
        this.fetch();
    },
    computed: {
        datares() {
            return this.$page.props.flash.datares;
        },
    },
    watch: {
        datares: {
            deep: true,
            handler(val = null) {
                if(val != null && val !== ''){
                    this.lists[this.index] = val.data;
                }
            },
        },
    },
    methods : {
        add(){
            this.$refs.new.show();
        },
        view(data){
            this.$refs.view.show(data);
        },
        fetch(page_url){
            page_url = page_url || '/staffs';
            axios.get(page_url,{
                params : {
                    keyword : this.keyword,
                    search: true
                }
            })
            .then(response => {
                if(response){
                    this.lists = response.data.data;                    
                }
            })
            .catch(err => console.log(err));
        },

        update(type,data,index){
            this.index = index;
            this.$refs.update.show(type,data);
        }
    }
}
</script>
<style>
.file-manager-sidebar {
  min-width: 450px;
  max-width: 450px;
  height: calc(100vh - 180px);
}
</style>
