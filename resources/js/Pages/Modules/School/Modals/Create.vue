<template>
    <b-modal v-model="showModal" title="Add School" size="lg" header-class="p-3 bg-light" class="v-modal-custom" modal-class="zoomIn" centered no-close-on-backdrop>
        <b-form class="customform mt-2">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger alert-dismissible alert-label-icon label-arrow" role="alert">
                        <i class="ri-error-warning-line label-icon"></i>
                        <strong>Please double check spellings and if the college is updated to university</strong>
                    </div>
                </div>
                <div class="col-md-12 mt-3">
                    <div class="form-group">
                        <label>School Name: <span v-if="form.errors" v-text="form.errors.name" class="haveerror"></span></label>
                        <input type="text" class="form-control" v-model="school.name" style="text-transform: uppercase;">
                    </div>
                </div>
                <div class="col-md-6 mt-3">
                    <div class="form-group">
                        <label>Class: <span v-if="form.errors" v-text="form.errors.class_id" class="haveerror"></span></label>
                            <multiselect 
                            v-model="school.class" 
                            :options="classes"
                            :allow-empty="false"
                            :show-labels="false"
                            label="name" track-by="id"
                            placeholder="Select Class">
                        </multiselect>
                    </div>
                </div>
                <div class="col-md-6 mt-3">
                    <div class="form-group">
                        <label>Shortcut: <span v-if="form.errors" v-text="form.errors.shortcut" class="haveerror"></span></label>
                        <input type="text" class="form-control" v-model="school.shortcut">
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
    props: ['classes'],
    data(){
        return {
            currentUrl: window.location.origin,
            school: {
                id: '',
                name: '',
                class: '',
                shortcut: ''
            },
            form: {},
            showModal: false,
            editable: false
        }
    },
    methods : {
        show(){
            this.school = {};
            this.editable = false;
            this.showModal = true;
            this.form = {};
        },
        edit(data){
            this.school = data;
            this.editable = true;
            this.showModal = true;
        },
        create(){
            this.form = this.$inertia.form({
                id: (this.school.id) ? this.school.id : '',
                name: (this.school.name) ? this.school.name : '',
                shortcut: (this.school.shortcut) ? this.school.shortcut : '',
                class_id: (this.school.class) ? this.school.class.id : '',
                type: 'create'
            })


            if(!this.editable){
                this.form.post('/schools',{
                    preserveScroll: true,
                    onSuccess: (response) => {
                        this.hide();
                        this.$emit('info',true);
                    },
                });
            }else{
                this.form.put('/schools/update',{
                    preserveScroll: true,
                    onSuccess: (response) => {
                        this.hide();
                        this.$emit('info',true);
                    }
                });
            }
        },
        hide(){
            this.school = {};
            this.editable = false;
            this.showModal = false;
        }
    }
}
</script>
