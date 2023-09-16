<template>

    <Head title="Scholars" />
    <PageHeader :title="title" :items="items" />
    <div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1">
        <div class="file-manager-content w-100 p-4 pb-0" style="height: calc(100vh - 180px)" ref="box">
            <b-row class="g-2 mb-3 mt-n1">
                 <b-col lg>
                    <div class="input-group mb-1">
                        <span class="input-group-text"> <i class="ri-search-line search-icon"></i></span>
                        <input type="text" v-model="keyword" placeholder="Search scholar" class="form-control" style="width: 30%;">
                        <select v-model="program" @change="fetch()" class="form-select" id="inputGroupSelect01" style="width: 120px;">
                            <option :value="null" selected>Select Program</option>
                            <option :value="list.id" v-for="list in listprograms" v-bind:key="list.id">{{list.name}}</option>
                        </select>
                        <select v-model="subprogram" @change="fetch()" class="form-select" id="inputGroupSelect01" style="width: 120px;">
                            <option :value="null" selected>Select Subprogram</option>
                            <option :value="list.id" v-for="list in listsubprograms" v-bind:key="list.id">{{list.name}}</option>
                        </select>
                        <select v-model="category" @change="fetch()" class="form-select" id="inputGroupSelect01" style="width: 120px;">
                            <option :value="null" selected>Select Category</option>
                            <option :value="list.id" v-for="list in categories" v-bind:key="list.id">{{list.name}}</option>
                        </select>
                        <select v-model="status" @change="fetch()" class="form-select" id="inputGroupSelect02" style="width: 120px;">
                            <option :value="null" selected>Select Status</option>
                            <option :value="list.id" v-for="list in statuses" v-bind:key="list.id">{{list.name}}</option>
                        </select>
                        <input type="text" v-model="year" placeholder="Year Awarded" class="form-control" style="width: 100px;">
                    </div>
                </b-col>
                <b-col lg="auto">
                    <b-button class="me-1" type="button" variant="info" @click="importModal()">
                        <i class="ri-filter-3-line align-bottom me-1"></i> import
                    </b-button>
                     <b-button class="me-1" type="button" variant="info" @click="bankModal()">
                        <i class="ri-filter-3-line align-bottom me-1"></i> Bank
                    </b-button>
                    <b-button class="me-1" type="button" variant="info" @click="updateModal()">
                        <i class="ri-filter-3-line align-bottom me-1"></i> Update
                    </b-button>
                </b-col>
            </b-row>
            <b-row>
                <div class="table-responsive">
                    <table class="table table-nowrap align-middle mb-0">
                        <thead class="table-light">
                            <tr class="fs-11">
                                <th></th>
                                <th style="width: 20%;">Name</th>
                                <th style="width: 20%;" class="text-center">Education</th>
                                <th style="width: 20%;" class="text-center">Address</th>
                                <th style="width: 10%;" class="text-center">Program</th>
                                <th style="width: 10%;" class="text-center">Category</th>
                                <th style="width: 10%;" class="text-center">Awarded Year</th>
                                <th style="width: 10%;" class="text-center">Status</th>
                                <th style="width: 10%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="list in lists" v-bind:key="list.id" :class="[(list.is_active == 0) ? 'table-warnings' : '']">
                                 <td>
                                    <div class="avatar-xs" v-if="list.profile.avatar == 'n/a'">
                                        <span class="avatar-title rounded-circle">{{list.profile.lastname.charAt(0)}}</span>
                                    </div>
                                    <div v-else>
                                        <img class="rounded-circle avatar-xs" :src="currentUrl+'/images/avatars/'+list.profile.avatar" alt="">
                                    </div>
                                </td>
                                <td>
                                    <h5 class="fs-13 mb-0 text-dark">{{list.profile.lastname}}, {{list.profile.firstname}} {{list.profile.middlename[0]}}.</h5>
                                    <p class="fs-11 text-muted mb-0">{{list.spas_id }}</p>
                                </td>
                                <td class="text-center">
                                    <h5 class="fs-11 mb-0 text-dark">{{ (!Object.keys(list.education.school).includes('name'))  ? list.education.school : list.education.school.name }} {{(!list.education.school.is_main) ? ' - '+list.education.school.campus : ''}}</h5> 
                                    <p class="fs-11 text-muted mb-0">{{ (!Object.keys(list.education.course).includes('name'))  ? list.education.course : list.education.course.name }}</p>
                                </td>
                                 <td class="text-center">
                                    <h5 class="fs-11 mb-0 text-dark">{{(list.address.address) ? list.address.address+',' : ''}} {{(list.address.barangay) ? list.address.barangay.name+',' : ''}} {{(list.address.municipality) ? list.address.municipality.name+',' : ''}}</h5>
                                    <p class="fs-11 text-muted mb-0">
                                        {{(list.address.province) ? list.address.province.name+',' : ''}}
                                        {{(list.address.region) ? list.address.region.region : ''}}
                                    </p>
                                </td>
                                <td class="text-center">
                                    <h5 class="fs-12 mb-0 text-dark">{{list.program.name}}</h5>
                                    <p class="fs-11 text-muted mb-0">{{list.subprogram.name }}</p>
                                </td>
                                <td class="text-center">{{list.category.name}}</td>
                                <td class="text-center">{{list.awarded_year}}</td>
                                <td class="text-center">
                                    <span :class="'badge '+list.status.color+' '+list.status.others">{{list.status.name}}</span>
                                </td>
                                <td class="text-end">
                                   
                                    <b-button variant="soft-primary" v-b-tooltip.hover title="Edit" size="sm" class="edit-list"><i class="ri-pencil-fill align-bottom"></i> </b-button>
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
    <Bank ref="bank"/>
    <Update ref="update"/>
</template>
<script>
import Update from './Modals/Status.vue';
import Bank from './Modals/Bank.vue';
import Import from './Modals/Import.vue';
import PageHeader from "@/Shared/Components/PageHeader.vue";
import Pagination from "@/Shared/Components/Pagination.vue";
export default {
    props: ['statuses','programs','dropdowns'],
    components: { PageHeader, Pagination, Import, Bank, Update },
    data() {
        return {
            currentUrl: window.location.origin,
            title: "List of Scholars",
            items: [{text: "List",href: "/"}, {text: "Course",active: true}],
            program: null,
            subprogram: null,
            category: null,
            status: null,
            year: null,
            sorty: 'asc',
            lists: [],
            meta: {},
            links: {},
            arr: {},
            arr2: {},
            keyword: null
        };
    },
    computed: {
        listprograms : function() {
            return this.programs.filter(x => x.is_sub === 1);
        },
        listsubprograms() {
            return this.programs;
        },
        categories : function() {
            return this.dropdowns.filter(x => x.classification === 'Category');
        },
    },
    watch: {
        keyword(newVal){
            this.checkSearchStr(newVal)
        },
        year(newVal){
            this.checkSearchStr(newVal)
        },
    },
    created(){
        this.fetch();
    },
    methods: {
        checkSearchStr: _.debounce(function(string) {
            this.fetch();
        }, 300),
        fetch(page_url) {
            let info = {
                'keyword' : this.keyword,
                'status' : (this.status ==  null) ? null : this.status, 
                'program' : (this.program ==  null) ? null : this.program, 
                'subprogram' : (this.subprogram ==  null) ? null : this.subprogram, 
                'category' : (this.category ==  null) ? null : this.category, 
                'year' : (this.year === '' || this.year == null) ? '' : this.year,
                'is_undergrad' : this.is_undergrad,
                'counts' : this.count2,
                'sorty' : this.sorty
            };

            info = (Object.keys(info).length == 0) ? '-' : JSON.stringify(info);
            let location = (Object.keys(this.arr).length == 0) ? '-' : JSON.stringify(this.arr);
            let education = (Object.keys(this.arr2).length == 0) ? '-' : JSON.stringify(this.arr2);

            page_url = page_url || '/scholars';
            axios.get(page_url, {
                params: {
                    lists: true,
                    counts: ((window.innerHeight-350)/56),
                    info : info,
                    location: location,
                    education: education,
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
        bankModal() {
            this.$refs.bank.show();
        },
        updateModal() {
            this.$refs.update.show();
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
