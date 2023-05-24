<template>
    <b-modal v-model="showModal" title="School Courses" size="lg" header-class="p-3 bg-light" class="v-modal-custom" modal-class="zoomIn" centered no-close-on-backdrop>
        <b-form class="customform mt-2">
            <div class="row">
                
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
