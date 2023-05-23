<template>
    <b-modal v-model="showModal" title="Add Campus" size="xl" header-class="p-3 bg-light" class="v-modal-custom" modal-class="zoomIn" centered no-close-on-backdrop>
        <b-form class="customform mt-2">
           
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger alert-dismissible alert-label-icon label-arrow" role="alert">
                        <i class="ri-error-warning-line label-icon"></i>
                         <!-- <strong>Please double check spellings and if the college is updated to university</strong> -->
                        <strong>Please choose on the options</strong>
                    </div>
                </div>
                 <b-col lg="6">
                    <div class="form-check card-radio">
                        <input id="paymentMethod01" @click="has_campuses = true" type="radio" class="form-check-input" :checked="(has_campuses) ? true : false"/>
                        <label class="form-check-label" for="paymentMethod01">
                        <span class="fs-16 text-muted me-2"><i class="ri-checkbox-circle-fill text-success align-bottom"></i></span>
                        <span class="fs-14 text-wrap">School has campuses</span>
                        </label>
                    </div>
                </b-col>
                <b-col lg="6">
                    <div class="form-check card-radio">
                        <input id="paymentMethod02" @click="has_campuses = false" type="radio" class="form-check-input" :checked="(!has_campuses && has_campuses != NULL) ? true : false"/>
                        <label class="form-check-label" for="paymentMethod02">
                        <span class="fs-16 text-muted me-2"><i class="ri-close-circle-fill text-danger align-bottom"></i></span>
                        <span class="fs-14 text-wrap">School has no Campuses</span>
                        </label>
                    </div>
                </b-col>
                <b-col lg="12 mt-4 mb-n4" v-if="has_campuses == false">
                    <div class="alert alert-success" role="alert">
                        <strong>School has no campuses! </strong> This will automatically make the school main campus
                        </div>
                </b-col>
            </div>

            <div class="row" v-if="has_campuses">
                <div class="col-md-12 mt-4">
                    <div class="alert alert-warning alert-dismissible alert-label-icon label-arrow" role="alert">
                        <i class="ri-error-warning-line label-icon"></i>
                         <!-- <strong>Please double check spellings and if the college is updated to university</strong> -->
                        <strong>Please indicate if school is main campus or not</strong>
                    </div>
                </div>
                 <b-col lg="6">
                    <div class="form-check card-radio">
                        <input id="paymentMethod04" @click="campus.is_main = true" type="radio" class="form-check-input" :checked="(campus.is_main) ? true : false"/>
                        <label class="form-check-label" for="paymentMethod04">
                        <span class="fs-16 text-muted me-2"><i class="ri-checkbox-circle-fill text-success align-bottom"></i></span>
                        <span class="fs-14 text-wrap">Main Campus</span>
                        </label>
                    </div>
                </b-col>
                <b-col lg="6">
                    <div class="form-check card-radio">
                        <input id="paymentMethod03" @click="campus.is_main = false" type="radio" class="form-check-input" :checked="(!campus.is_main && campus.is_main != NULL) ? true : false"/>
                        <label class="form-check-label" for="paymentMethod03">
                        <span class="fs-16 text-muted me-2"><i class="ri-close-circle-fill text-danger align-bottom"></i></span>
                        <span class="fs-14 text-wrap">Not Main Campus</span>
                        </label>
                    </div>
                </b-col>

                <!-- <div class="col-md-12">
                    <div class="form-group mt-4">
                        <div class="form-check">
                            <input v-model="campus.is_main" class="form-check-input" type="checkbox" id="gridCheck"/>
                            <label class="form-check-label fs-12 text-info" for="gridCheck">
                                is this the main campus? (Please check it, if not sure please ask school coordinator)
                            </label>
                        </div>
                    </div>
                </div> -->
            </div>

            <div class="row mt-4" v-if="(has_campuses != NULL && campus.is_main != NULL) || (has_campuses == false) || (has_campuses == true && campus.is_main != NULL)">
                <div class="col-md-12 mt-4">
                    <div class="form-group">
                        <label>Campus/Municipality/City Name: <span v-if="form.errors" v-text="form.errors.campus" class="haveerror"></span></label>
                        <input type="text" class="form-control" v-model="campus.campus" style="text-transform: uppercase;">
                    </div>
                </div>
                 <div class="col-md-12 mt-2">
                    <div class="form-group">
                        <label>Old Name: <span v-if="form.errors" v-text="form.errors.oldname" class="haveerror"></span></label>
                        <input type="text" class="form-control" v-model="campus.old" style="text-transform: uppercase;">
                    </div>
                </div>
                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <label>Term: <span v-if="form.errors" v-text="form.errors.term_id" class="haveerror"></span></label>
                            <multiselect 
                            v-model="campus.term" 
                            :options="terms"
                            :allow-empty="false"
                            :show-labels="false"
                            label="name" track-by="id"
                            placeholder="Select Term">
                        </multiselect>
                    </div>
                </div>
                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <label>Grading: <span v-if="form.errors" v-text="form.errors.grading_id" class="haveerror"></span></label>
                            <multiselect 
                            v-model="campus.grading" 
                            :options="gradings"
                            :allow-empty="false"
                            :show-labels="false"
                            label="name" track-by="id"
                            placeholder="Select Grading">
                        </multiselect>
                    </div>
                </div>

                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <label>Region: <span v-if="form.errors" v-text="form.errors.region_code" class="haveerror"></span></label>
                            <multiselect 
                            @input="onChangeRegion(region.code)"
                            v-model="campus.region" 
                            :options="regions"
                            :allow-empty="false"
                            :show-labels="false"
                            label="region" track-by="code"
                            placeholder="Select Region">
                        </multiselect>
                    </div>
                </div>
                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <label>Province: <span v-if="form.errors" v-text="form.errors.province_code" class="haveerror"></span></label>
                            <multiselect 
                            @input="onChangeProvince(province.code)"
                            v-model="campus.province" 
                            :options="provinces"
                            :allow-empty="false"
                            :show-labels="false"
                            label="name" track-by="code"
                            placeholder="Select Province">
                        </multiselect>
                    </div>
                </div>
                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <label>Municipality: <span v-if="form.errors" v-text="form.errors.municipality_code" class="haveerror"></span></label>
                            <multiselect 
                            v-model="campus.municipality" 
                            :options="municipalities"
                            :allow-empty="false"
                            :show-labels="false"
                            label="name" track-by="code"
                            placeholder="Select Municipality">
                        </multiselect>
                    </div>
                </div>
                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <label>Address: <span v-if="form.errors" v-text="form.errors.address" class="haveerror"></span></label>
                        <input type="text" class="form-control" v-model="campus.address" style="text-transform: uppercase;">
                    </div>
                </div>
            </div>
        </b-form>
        <template v-slot:footer>
            <b-button @click="hide()" variant="light" block>Cancel</b-button>
            <b-button @click="create('ok')" variant="primary" block>Save</b-button>
        </template>
    </b-modal>
</template>
<script>
import Multiselect from '@suadelabs/vue3-multiselect';
export default {
    components : { Multiselect },
    props: ['gradings','terms','regions'],
    data(){
        return {
            currentUrl: window.location.origin,
            id: '',
            has_campuses: null,
            campus: {
                is_main: null,
                old: '',
                campus: '',
                grading: '',
                term: '',
                region: '',
                province: '',
                municipality: '',
                address: ''
            },
            provinces: [],
            municipalities: [],
            form: {},
            showModal: false,
        }
    },
    watch: {
        'campus.region'(){
            if(this.campus.region){
                this.campus.province = '';
                this.campus.municipality = '';
                this.fetchProvince(this.campus.region.code);
            }
        },
        'campus.province'(){
            if(this.campus.province){
                this.campus.municipality = '';
                this.fetchMunicipality(this.campus.province.code);
            }
        }
    },
    methods : {
        show(id){
            this.id = id;
            this.campus = { is_main : null};
            this.has_campuses = null;
            this.provinces = [];
            this.municipalities = [];
            this.form = {};
            this.showModal = true;
        },
        create(){
            this.form = this.$inertia.form({
                is_main: (this.campus.is_main == true) ? true : (this.has_campuses == false) ? true : false,
                campus: (this.campus.campus) ? this.campus.campus : '',
                oldname: (this.campus.old) ? this.campus.old : '',
                address: (this.campus.address) ? this.campus.address : '',
                school_id: this.id,
                term_id: (this.campus.term) ? this.campus.term.id : '',
                grading_id: (this.campus.grading) ? this.campus.grading.id : '',
                region_code: (this.campus.region) ? this.campus.region.code : '',
                province_code: (this.campus.province) ? this.campus.province.code : '',
                municipality_code: (this.campus.municipality) ? this.campus.municipality.code : '',
                type: 'campus'
            })

            this.form.post('/schools',{
                preserveScroll: true,
                onSuccess: (response) => {
                    this.hide();
                    this.$emit('info',true);
                },
            });
        },
        fetchProvince($id){
            axios.get(this.currentUrl + '/lists/provinces/'+$id)
            .then(response => {
                this.provinces = response.data.data;
            })
            .catch(err => console.log(err));
        },

        fetchMunicipality($id){
            axios.get(this.currentUrl + '/lists/municipalities/'+$id)
            .then(response => {
                this.municipalities = response.data.data;
            })
            .catch(err => console.log(err));
        },
        hide(){
            this.campus = {};
            this.provinces = [];
            this.municipalities = [];
            this.showModal = false;
        }
    }
}
</script>
