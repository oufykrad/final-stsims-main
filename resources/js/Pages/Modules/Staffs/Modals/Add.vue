<template>
    <b-modal v-model="showModal"  @ok="create($event)" hide-footer body-class="p-0" size="lg" header-class="p-3" id="myModal"
        class="v-modal-custom" title="New Staff" content-class="border-0" centered>
        <b-alert variant="success" class="rounded-0 mb-0" show>
            <p class="mb-0">Please fill up all and <span class="fw-semibold">correctly</span></p>
        </b-alert>
        <div class="modal-body">
            <form class="customform">
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="profile-user position-relative d-inline-block mx-auto mb-2">
                                <img src="/images/users/user-dummy-img.jpg"
                                    class="rounded-circle avatar-lg img-thumbnail user-profile-image"
                                    alt="user-profile-image">
                                <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                                    <input id="profile-img-file-input" type="file"  @change="previewImage"
                                        class="profile-img-file-input">
                                    <label for="profile-img-file-input"
                                        class="profile-photo-edit avatar-xs">
                                        <span class="avatar-title rounded-circle bg-light text-body">
                                            <i class="ri-camera-fill"></i>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Firstname: <span v-if="form.errors" v-text="form.errors.firstname" class="haveerror"></span></label>
                                    <input type="text" class="form-control" v-model="user.firstname" style="text-transform: capitalize;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Middlename: <span v-if="form.errors" v-text="form.errors.middlename" class="haveerror"></span></label>
                                    <input type="text" class="form-control" v-model="user.middlename" style="text-transform: capitalize;"> 
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Lastname: <span v-if="form.errors" v-text="form.errors.lastname" class="haveerror"></span></label>
                                    <input type="text" class="form-control" v-model="user.lastname" style="text-transform: capitalize;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Suffix: <span v-if="form.errors" v-text="form.errors.suffix" class="haveerror"></span></label>
                                    <input type="text" class="form-control" v-model="user.lastname" style="text-transform: capitalize;">
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email: <span v-if="form.errors" v-text="form.errors.email" class="haveerror"></span></label>
                                    <input type="email" class="form-control" v-model="user.email" style="text-transform: lowercase;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Mobile No.: <span v-if="form.errors" v-text="form.errors.mobile" class="haveerror"></span></label>
                                    <input type="text" class="form-control" v-model="user.mobile">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Username: <span v-if="form.errors" v-text="form.errors.username" class="haveerror"></span></label>
                                    <input type="text" class="form-control" v-model="user.username" style="text-transform: lowercase;">
                                </div>
                            </div>
                            <div class="col-md-6" style="margin-top: -1px; margin-bottom: -15px;">
                                <div class="row" style="margin-top: 20px;">
                                    <div class="col-md-4">
                                        <div class="custom-control custom-radio mb-3">
                                            <input type="radio" id="customRadio1" class="custom-control-input me-2"  value="M" v-model="user.gender">
                                            <label class="custom-control-label" for="customRadio1">Male</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="custom-control custom-radio mb-3">
                                            <input type="radio" id="customRadio2" class="custom-control-input me-2" value="F" v-model="user.gender">
                                            <label class="custom-control-label" for="customRadio2">Female</label>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                             <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>Region: <span v-if="form.errors" v-text="form.errors.agency_id" class="haveerror"></span></label>
                                    <Multiselect 
                                    v-model="user.agency" 
                                    :options="agencies"
                                    :allow-empty="false"
                                    :show-labels="false"
                                     label="name" track-by="id"
                                    placeholder="Select Role"/>
                                    <!-- :custom-label="nameWithLang" -->
                                </div>
                            </div>
                            <!-- <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>Role: <span v-if="form.errors" v-text="form.errors.role" class="haveerror"></span></label>
                                    <Multiselect 
                                    v-model="user.role" 
                                    :options="roles"
                                    :allow-empty="false"
                                    :show-labels="false"
                                    placeholder="Select Role"/>
                                </div>
                            </div> -->

                        </div>
                    </div>

                </div>
                <div class="modal-footer v-modal-footer">
                    <button class="btn btn-light btn-md" type="button"><div class="btn-content">Close </div></button>
                    <button class="btn btn-primary btn-md" @click="create()" :disabled="form.processing" type="button"><div class="btn-content">Submit</div></button>
                </div>
            </form>
        </div>
        
        
    </b-modal>
</template>
<script>
import Multiselect from '@suadelabs/vue3-multiselect';
export default {
    props: ['agencies'],
    components : { Multiselect },
    data(){
        return {
            form : {},
            currentUrl: window.location.origin,
            user: {
                id: '',
                email: '',
                firstname: '',
                lastname: '',
                middlename: '',
                gender: '',
                mobile: '',
                avatar: '',
                profile_id: '',
                role: 'Staff',
                agency: '',
                username: ''
            },
            roles: ['Regional Director', 'Accountant', 'Scholarship Coordinator', 'Scholarship Staff'],
            showModal : false,
            editable: false,
        }
    },

    watch: {
        'user.agency'(){
            if(this.user.agency){
                console.log(this.user.agency);
                this.user.firstname = this.user.agency.acronym;
                this.user.lastname = this.user.agency.code;
                this.user.middlename = 'M';
                this.user.email = this.user.agency.acronym+'@gmail.com';
                this.user.username = this.user.agency.acronym;
                this.user.gender = 'M';
                this.user.mobile = '091234567'+this.user.agency.id;
            }
        }
    },

    methods : {
        show(){
            this.showModal = true;
        },
        // nameWithLang ({ region,name }) {
        //     return `${region} — ${name}`
        // },
        previewImage(e) {
            var preview = document.querySelector(".user-profile-image");
            var file = document.querySelector(".profile-img-file-input").files[0];
            var reader = new FileReader();
            
            reader.addEventListener(
                "load",
                function () {
                    preview.src = reader.result;
                },
                false
            );

            reader.onload = (e) => {
                this.user.avatar = e.target.result;
            };

            if (file) {
                reader.readAsDataURL(file);
            }
        },

        create() {
            this.form = this.$inertia.form({
                id: this.user.id,
                username: this.user.username,
                firstname: this.user.firstname,
                lastname: this.user.lastname,
                middlename: this.user.middlename,
                email: this.user.email,
                mobile: this.user.mobile,
                gender: this.user.gender,
                role: this.user.role,
                agency_id: (this.user.agency) ? this.user.agency.id : '',
                profile_id: this.user.profile_id,
                img: this.user.avatar,
                editable: this.editable
            })

            if(!this.editable){
                this.form.post('/staffs',{
                    forceFormData: true,
                    preserveScroll: true,
                    onSuccess: (response) => {
                        this.hide();
                    }
                });
            }else{
                this.form.put('/staffs/update',{
                    preserveScroll: true,
                    onSuccess: (response) => {
                        this.hide();
                    }
                });
            }
        },
        hide(){
            this.showModal = false;
        }
    },
}
</script>
<style>
.multiselect__single {
    font-size: 11px;
}
</style>