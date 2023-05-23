<template>
    <b-modal v-model="showModal" title="Update Incomplete Information" header-class="p-3 bg-light" class="v-modal-custom" modal-class="zoomIn" centered no-close-on-backdrop>    
        <blockquote class="p-3 border-light border rounded">
            <div class="d-flex">
                <div class="mr-3"><i class="bx bxs-quote-alt-left text-primary font-size-14"></i></div>
                <div>
                    <p class="mb-1 fw-bold text-primary">{{ user.profile.lastname}}, {{ user.profile.firstname}} {{ user.profile.middlename }} <span class="text-muted font-size-11 ml-2">({{user.program.name}})</span> </p>
                </div>
            </div>
            <div class="row font-size-11 mt-2">
                <div class="col-sm-12">
                    <p class="fw-bold text-danger mb-0">{{user.address.name.toUpperCase()}}</p>
                    <p class="fw-bold text-dark mb-0">{{ (!Object.keys(user.education.school).includes('name'))  ? user.education.school : user.education.school.name }}</p>
                    <p class="text-dark mb-0">{{user.education.course }} </p>
                </div>
            </div>
        </blockquote>

        <b-form class="customform mb-2">
            <div class="row customerform">
                <div class="col-md-12" v-if="!user.education.has_school">
                    <label>School: <span v-if="errors.length > 0" class="haveerror">({{ errors[0].school_id }})</span></label>
                    <multiselect v-model="school" id="ajax" label="name" track-by="id"
                        placeholder="Search School" open-direction="bottom" :options="schools"
                        :searchable="true" 
                        :allow-empty="false"
                        :show-labels="false"
                        @search-change="asyncSchool">
                    </multiselect> 
                </div>
                <div class="col-md-12" v-if="!user.education.has_course">
                    <label>Course: <span v-if="errors.length > 0" class="haveerror">({{ errors[0].course_id }})</span></label>
                    <multiselect v-model="course" id="ajax" label="name" track-by="id"
                        placeholder="Search Course" open-direction="bottom" :options="courses"
                        :searchable="true" 
                        :allow-empty="false"
                        :show-labels="false"
                        @search-change="asyncCourse">
                    </multiselect> 
                </div>
                <div class="col-md-12" v-if="!user.education.has_level">
                    <label>Level: <span v-if="errors.length > 0" class="haveerror">({{ errors[0].level_id }})</span></label>
                    <multiselect 
                        v-model="level" 
                        id="ajax" 
                        label="name" track-by="id"
                        placeholder="Search Level" 
                        open-direction="bottom" 
                        :options="levels"
                        :searchable="true" 
                        :allow-empty="false"
                        :show-labels="false">
                    </multiselect> 
                </div>
                <div class="col-md-12 mt-2 mb-1" v-if="user.status.name == 'Graduated'">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" v-model="has_award" id="gridCheck"/>
                        <label class="form-check-label" for="gridCheck">Does the scholar has Academic award?</label>
                    </div>
                </div>
                <div class="col-md-12" v-if="user.status.name == 'Graduated' && has_award == true">
                    <label>Award: <span v-if="errors.length > 0" class="haveerror">({{ errors[0].award_id }})</span></label>
                    <multiselect 
                        v-model="award" 
                        id="ajax" 
                        label="name" track-by="id"
                        placeholder="Select Award" 
                        open-direction="bottom" 
                        :options="awards"
                        :searchable="true" 
                        :allow-empty="false"
                        :show-labels="false">
                    </multiselect> 
                </div>
            </div>
        </b-form>
        <template v-slot:footer>
            <b-button @click="hide()" variant="light" block>Cancel</b-button>
            <b-button @click="create('ok')" variant="primary" :disabled="form.processing" block>Save</b-button>
        </template>
    </b-modal>
</template>
<script>
import Multiselect from '@suadelabs/vue3-multiselect';
export default {
    components: { Multiselect },
    props: ['dropdowns'],
    data(){
        return {
            currentUrl: window.location.origin,
            errors: [],
            user: {
                program: {},
                profile: {},
                address: {name: ''},
                status: {},
                education: {school: ''},
                course: {}
            },
            form : {},
            profile_id : '',
            lrn: '',
            program_id: '',
            schools: [],
            courses: [],
            level: '',
            school: '',
            course: '',
            award: '',
            has_award: false,
            showModal: false
        }
    },
    computed:{
        levels : function() {
            return this.dropdowns.filter(x => x.classification === 'Level');
        },
        awards : function() {
            return this.dropdowns.filter(x => x.classification === 'Award');
        }
    },
    methods : {
        show(data){
            this.user = data;
            this.errors = [];
            this.showModal = true;
        },
        hide(){
            this.showModal = false;
        },
        create(){
            let data = new FormData();

            data.append('id', this.user.id);
            data.append('is_completed', 1);
            data.append('type', 'old');
            data.append('editable', 'information');
            
            (!this.user.education.has_school) ? data.append('school_id',(this.school != '') ? this.school.id : '') : '';
            (!this.user.education.has_course) ? data.append('course_id', (this.course != '') ? this.course.id : '') : '';
            (!this.user.education.has_level) ? data.append('level_id', (this.level != '') ? this.level.id : '') : '';
            (this.has_award) ? data.append('award_id', (this.award != '') ? this.award.id : '') : '';

            this.$inertia.post('/scholars',data,{
                preserveScroll: true,
                forceFormData: true,
                onSuccess: (response) => {
                    this.clear();
                },
                onError: (response) =>{
                    this.errors.push(response);
                    // console.log(this.errors[0].course_id)
                }
            });
        },
        asyncSchool(value) {
            if(value.length > 5){
                axios.post(this.currentUrl + '/lists/search/schools', {
                    word: value,
                })
                .then(response => {
                    this.schools = response.data.data;
                })
                .catch(err => console.log(err));
            }
        },
        asyncCourse(value) {
            if(value.length > 5){
                axios.post(this.currentUrl + '/lists/search/courses', {
                    word: value,
                    school_id: (!Object.keys(this.user.education.school).includes('id')) ? this.school.id : this.user.education.school.id,
                })
                .then(response => {
                    this.courses = response.data.data;
                })
                .catch(err => console.log(err));
            }
        },
        clear(){
            this.school = '';
            this.course = '';
            this.award = '';
            this.showModal = false;
        }
    }
}
</script>
