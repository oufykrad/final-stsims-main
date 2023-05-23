<template>
    <b-modal v-model="showModal" title="Print" header-class="p-3 bg-light" class="v-modal-custom" modal-class="zoomIn" centered no-close-on-backdrop>    
        <div class="row p-2">
            <div class="col-md-12">
                <div role="alert" aria-live="polite" aria-atomic="true" class="alert alert-warning">You can filter by using the dropdown. Can be Empty</div>
                <div class="row g-3">
                    <div class="col-md-12" v-if="type == 'scholars'">
                        <multiselect 
                            v-model="status" 
                            id="ajax" 
                            label="name" track-by="id"
                            placeholder="Select Status" 
                            open-direction="bottom" 
                            :options="statuses"
                            :searchable="true" 
                            :allow-empty="true"
                            :show-labels="false">
                        </multiselect> 
                    </div>
                    <div class="col-md-12 mt-2 mb-n2" v-if="type == 'scholars'">
                        <multiselect 
                            v-model="program" 
                            id="ajax" 
                            label="name" track-by="id"
                            placeholder="Select Category" 
                            open-direction="bottom" 
                            :options="programs"
                            :searchable="true" 
                            :allow-empty="true"
                            :show-labels="false">
                        </multiselect> 
                    </div>
                    <div class="col-md-6" style="padding-right: 5px;">
                        <div class="form-group">
                            <date-picker
                                v-model:value="from"
                                type="year" format="YYYY"
                                lang="en"
                                placeholder="From Year"
                                :clearable="false"
                                >
                            </date-picker>
                        </div>
                    </div>
                    <div class="col-md-6" style="padding-right: 5px;">
                        <date-picker
                            v-model:value="to"
                            type="year" format="YYYY"
                            lang="en"
                            placeholder="To Year"
                            :clearable="false"
                            >
                        </date-picker>
                    </div>
                </div>
            </div>
        </div>
         <template v-slot:footer>
            <b-button @click="hide()" variant="light" block>Cancel</b-button>
            <b-button @click="generate('ok')" variant="primary" :disabled="form.processing" block>Generate</b-button>
        </template>
    </b-modal>
</template>
<script>
import DatePicker from "vue-datepicker-next";
import 'vue-datepicker-next/index.css';
import Multiselect from '@suadelabs/vue3-multiselect';
export default {
    components: {DatePicker, Multiselect},
    props: ['programs','dropdowns'],
    data(){
        return {
            currentUrl: window.location.origin,
            errors: [],
            type: '',
            status: '',
            program: '',
            from: '',
            to: '',
            form: {},
            showModal: false
        }
    },
    computed:{
        statuses : function() {
            return this.dropdowns.filter(x => x.classification === 'Status');
        }
    },
    methods: {
        set(type){
            this.showModal = true;
            this.status = ''; 
            this.program = ''; 
            this.from = ''; 
            this.to = '';
            this.type = type;
            if(type != 'scholars'){
                let t = this.statuses.filter(x => x.name === 'Graduated');
                this.status = t[0]; 
            }
        },
        generate(){
            let info = {
                'status' : (this.status != '' && this.status != null) ? this.status.id : '',
                'program' : (this.program != '' && this.program != null) ? this.program.id : '',
                'from' : (this.from != '') ? new Date(this.from).toLocaleDateString("af-ZA") : '',
                'to' : (this.to != '') ? new Date(this.to).toLocaleDateString("af-ZA") : '',
                'is_undergrad' : '',
                'type' : this.type
            };
            info = JSON.stringify(info);
            window.open(this.currentUrl + '/scholar/generate/'+info);
        },
        hide(){
            this.showModal = false;
        }
    }
}
</script>
<style>
.wew {
    height: 100px;
}
</style>