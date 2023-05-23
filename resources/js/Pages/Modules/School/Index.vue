<template>

    <Head title="Schools" />
    <PageHeader :title="title" :items="items" />
    <div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1">
        <div class="file-manager-content w-100 p-4 pb-0" style="height: calc(100vh - 180px)" ref="box">
            <b-row class="g-2 mb-3 mt-n1">
                <b-col lg>
                    <div class="input-group mb-1">
                        <span class="input-group-text"> <i class="ri-search-line search-icon"></i></span>
                        <input type="text" v-model="keyword" placeholder="Search course" class="form-control" style="width: 40%;">
                    </div>
                </b-col>
                <b-col lg="auto">
                    <b-button class="me-1" type="button" variant="light" @click="importModal()">
                        <i class="ri-upload-cloud-2-fill align-bottom me-1"></i> Import
                    </b-button>
                     <b-button class="me-1" type="button" variant="primary" @click="create()">
                        <i class="ri-add-circle-fill align-bottom me-1"></i> Create
                    </b-button>
                </b-col>
            </b-row>
            <b-row>
                <div class="table-responsive">
                    <table class="table table-nowrap align-middle mb-0">
                        <thead class="table-light">
                            <tr class="fs-11">
                                <th style="width: 30%;">Name</th>
                                <th style="width: 15%;" class="text-center">Shortcut</th>
                                <th style="width: 15%;" class="text-center">Class</th>
                                <th style="width: 15%;" class="text-center">Status</th>
                                <th style="width: 15%;" class="text-center">Campus</th>
                                <th style="width: 10%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="list in lists" v-bind:key="list.id" :class="[(list.is_active == 0) ? 'table-warnings' : '']">
                                <td>{{list.name}}</td>
                                <td class="text-center">{{list.shortcut}}</td>
                                <td class="text-center">{{list.class.name}}</td>
                                <td class="text-center">
                                    <span v-if="list.is_active" class="badge bg-success">Active</span>
                                    <span v-else class="badge bg-danger">Inactive</span>
                                </td>
                                <td class="text-center">{{list.campuses.length}}</td>
                                <td class="text-end">
                                    <b-button @click="edit(list)" variant="soft-primary" v-b-tooltip.hover title="Edit" size="sm" class="edit-list me-1"><i class="ri-pencil-fill align-bottom"></i> </b-button>
                                    <Link :href="`/schools/${list.code}`"><b-button variant="soft-info" v-b-tooltip.hover title="View" size="sm" class="edit-list"><i class="ri-eye-fill align-bottom"></i> </b-button></Link>
                                    
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <Pagination class="ms-2 me-2" v-if="meta" @fetch="fetch" :lists="lists.length" :links="links" :pagination="meta" />
                </div>
            </b-row>
        </div>
    </div>
    <Import ref="import"/>
    <Create :classes="classes" ref="create" @info="fetch()"/>
</template>
<script>
import Create from './Modals/Create.vue';
import Import from './Modals/Import.vue';
import PageHeader from "@/Shared/Components/PageHeader.vue";
import Pagination from "@/Shared/Components/Pagination.vue";
export default {
    components: { PageHeader, Pagination, Import, Create },
    props: ['dropdowns'],
    data() {
        return {
            currentUrl: window.location.origin,
            title: "List of Schools",
            items: [{text: "List",href: "/"}, {text: "School",active: true}],
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
    },
    watch: {
        keyword(newVal){
            this.checkSearchStr(newVal)
        }
    },
    created(){
        this.fetch();
    },
    methods: {
        checkSearchStr: _.debounce(function(string) {
            this.fetch();
        }, 300),
        fetch(page_url) {
            page_url = page_url || '/schools';
            axios.get(page_url, {
                params: {
                    lists: true,
                    counts: ((window.innerHeight-350)/56),
                    keyword: this.keyword
                }
            })
            .then(response => {
                this.lists = response.data.data;
                this.meta = response.data.meta;
                this.links = response.data.links;
            })
            .catch(err => console.log(err));
        },
        percentage(data) {
            return Math.floor((data / this.total) * 100) + '%';
        },
        importModal() {
            this.$refs.import.show();
        },
        create(){
            this.$refs.create.show();
        },
        edit(data){
            this.$refs.create.edit(data);
        },
        print(type){
            this.$refs.print.set(type);
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
