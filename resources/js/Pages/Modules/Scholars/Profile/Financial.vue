<template>
    <b-row>
        <b-col xxl="12">
            <b-card no-body>
                <b-card-body style="height: calc(100vh - 380px);">
                    <div class="input-group mb-1">
                        <label class="input-group-text bg-light"> <i class='bx bx-search-alt'></i></label> 
                        <select v-model="ay" @change="fetch()" placeholder="Choose Academic Year" class="form-select" style="width: 120px;">
                            <option :value="null" selected>Select Academic Year</option>
                            <option :value="m.academic_year" v-for="(m,index) in ays" v-bind:key="index">{{ m.academic_year }}</option>
                        </select>
                        <select v-model="semester" @change="fetch()" placeholder="Choose Semester" class="form-select" style="width: 120px;">
                            <option :value="null" selected>Select Semester</option>
                            <option :value="m.id" v-for="(m,index) in types" v-bind:key="index">{{ m.name }}</option>
                        </select>
                        <b-button type="button" variant="info" @click="print()">
                            <i class="ri-printer-fill align-bottom me-1"></i> Print Financial Breakdown
                        </b-button>
                    </div>
                    <div class="table-responsive mt-3" data-simplebar style="max-height: calc(100vh - 480px);">
                        <table class="table table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr class="fs-11">
                                    <th>Academic Year</th>
                                    <th class="text-center">Semester</th>
                                    <th class="text-center">Total</th>
                                    <!-- <th class="text-center">Type</th> -->
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="list in lists" v-bind:key="list.id">
                                    <td>{{ list.academic_year }}</td>
                                    <td class="text-center">{{ list.semester.name }}</td>
                                    <td class="text-center">₱{{ formatMoney(list.benefits_sum_amount)}}</td>
                                    <!-- <td class="text-center">
                                        <span v-if="!list.release_type" class="badge bg-info">Partial</span>
                                        <span v-else class="badge bg-success">Full</span>
                                    </td> -->
                                    <td class="text-end">
                                        <b-button @click="print(list)" variant="soft-danger" v-b-tooltip.hover title="Print" size="sm" class="edit-list me-1"><i class="ri-printer-fill align-bottom"></i> </b-button>
                                        <b-button @click="view(list)" variant="soft-info" v-b-tooltip.hover title="View" size="sm" class="edit-list me-1"><i class="ri-eye-fill align-bottom"></i> </b-button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </b-card-body>
            </b-card>
        </b-col>
    </b-row>

    <b-modal v-model="showModal" title="View Financial Benefit" header-class="p-3 bg-light" size="xl" class="v-modal-custom" modal-class="zoomIn" centered hide-footer>    
        <b-form class="customform mb-2">
            <b-row class="mt-2 mb-2">
                <b-col lg="4">
                    <div class="p-2 border border-dashed rounded">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-2">
                                <div class="avatar-title rounded bg-transparent text-success fs-24">
                                    <i class="ri-calendar-line"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1">Academic Year :</p>
                                <h6 class="mb-0">{{ selected.academic_year }}</h6>
                            </div>
                        </div>
                    </div>
                </b-col>
                <b-col lg="4">
                    <div class="p-2 border border-dashed rounded">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-2">
                                <div class="avatar-title rounded bg-transparent text-success fs-24">
                                    <i class="ri-calendar-todo-line"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1">Semester :</p>
                                <h6 class="mb-0">{{ (selected.semester) ? selected.semester.name : '' }}</h6>
                            </div>
                        </div>
                    </div>
                </b-col>
                <b-col lg="4">
                    <div class="p-2 border border-dashed rounded">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-2">
                                <div class="avatar-title rounded bg-transparent text-success fs-24">
                                    <i class="ri-briefcase-2-fill"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1">Total :</p>
                                <h6 class="mb-0">₱{{ formatMoney(selected.benefits_sum_amount) }}</h6>
                            </div>
                        </div>
                    </div>
                </b-col>
            </b-row>
            <div class="row">
                <div class="col-md-12">
                    <hr class="text-muted"/>
                    <div class="table-responsive">
                        <table class="table table-bordered table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr class="fs-11">
                                    <th>Benefit</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="list in selected.benefits" v-bind:key="list.id">
                                    <td>
                                        <h5 class="fs-13 mb-0 text-dark" v-if="list.benefit">
                                            {{list.benefit.name}} 
                                            <span v-if="list.benefit.name == 'Stipend'" class="text-muted fs-11">({{list.month}})</span>
                                        </h5>
                                    </td>
                                    <td class="text-center" v-if="list.benefit">₱{{formatMoney(list.amount,list.benefit.name)}}</td>
                                    <td class="text-center">
                                        <span v-if="!list.release_type" class="badge bg-info">Partial</span>
                                        <span v-else class="badge bg-success">Full</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </b-form>
    </b-modal>
</template>
<script>
export default {
    props: ['benefits','privileges','ays','types','id'],
    data(){
        return {
            currentUrl: window.location.origin,
            semester: null,
            ay: null,
            privilege: null,
            lists: [],
            showModal: false,
            selected: ''
        }
    },
    created(){
        this.lists = this.benefits;
    },
    methods : {
        formatMoney(value) {
            let val = (value/1).toFixed(2).replace(',', '.')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        },
         fetch(page_url) {
            page_url = page_url || '/scholars';
            axios.get(page_url, {
                params: {
                    ay: this.ay,
                    semester: this.semester,
                    id: this.id,
                    search: 'benefits'
                }
            })
            .then(response => {
                this.lists = response.data;
            })
            .catch(err => console.log(err));
        },
        view(data){ 
            console.log(data.benefits);
            this.selected = data;
            this.showModal = true;
        },
        print(){
            window.open(this.currentUrl + '/print/'+this.id);
        }
        // check(data){
        //     if(data == 'Stipend'){
        //         var test = this.selected.benefits.filter(x => x.benefit.name === 'Stipend');
        //         return 'Stipend (x' + test.length +')';
        //     }else{
        //         return data;
        //     }
        // },
        // total(data,title){
        //     if(title == 'Stipend'){
        //         var test = this.selected.benefits.filter(x => x.benefit.name === 'Stipend');
        //         let tot =  test.reduce((sum, t) => {
        //             return sum += parseInt(t.amount);
        //         }, 0);
        //         return tot;
        //     }else{
        //         return data;
        //     }
        // }
    }
}
</script>