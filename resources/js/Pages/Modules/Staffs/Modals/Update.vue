<template>
    <b-modal v-model="showModal" hide-footer hide-header-close body-class="p-0" class="v-modal-custom"
         content-class="border-0" centered>

        <!-- <div class="modal-body">

            <div class="card-body">
                <a class="d-flex align-items-center collapsed" href="#" target="_self" role="button"
                    data-bs-toggle="collapse" data-bs-target="#leadDiscovered1" aria-expanded="false">
                    <div class="flex-shrink-0"><img :src="currentUrl+'/images/avatars/'+list.avatar" alt=""
                            class="avatar-xs rounded-circle"></div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="fs-14 mb-0">{{ list.name }}</h6>
                        <p class="text-muted mb-0">{{ list.role }}</p>
                    </div>
                </a>
            </div>

        </div> -->


        <div class="card mb-1 mt-n3">
            <div class="card-body">
                <a class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <img :src="currentUrl+'/images/avatars/'+list.avatar" alt="" class="avatar-xs rounded-circle">
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="fs-14 mb-0">{{list.name}}</h6>
                        <p class="text-muted mb-0">{{list.role}}</p>
                    </div>
                </a>
            </div>
            <div id="leadDiscovered3" class="collapse border-top border-top-dashed show" is-nav="false" v-if="type == 'status'">
                <div class="card-body">
                    <p class="mb-0" v-if="list.is_active == 1">Are you sure you want to <span class="fw-bold text-danger">deactivate</span> {{list.firstname}} {{list.lastname}}?</p>
                    <p class="mb-0" v-else>Are you sure you want to <span class="fw-bold text-success">activate</span> {{list.firstname}} {{list.lastname}}?</p>
                </div>
                <div class="p-3 bg-soft-warning" v-if="list.is_active == 1">
                    <h6 class="mb-0 text-danger text-center fs-10">This should blocked user from accessing the system.</h6>
                </div>
                <div class="p-3 bg-soft-warning" v-else>
                    <h6 class="mb-0 text-danger text-center fs-10">This should allow the user to access the system.</h6>
                </div>
                <div class="card-footer hstack gap-2 float-end">
                    <button class="btn btn-light btn-sm w-md" @click="hide()" type="button">
                        <div class="btn-content">Cancel</div>
                    </button>
                    <button class="btn btn-info btn-sm w-md" type="button" :disabled="form.processing" @click="create()">
                        <div class="btn-content">Submit</div>
                    </button>
                </div>
            </div>
            <div id="leadDiscovered3" class="collapse border-top border-top-dashed show text-center" is-nav="false" v-else>
                <div v-if="!status" class="mt-3 mb-2">
                    <div class="avatar-md mx-auto">
                        <div class="avatar-title rounded-circle bg-light">
                            <i class="bx bxs-envelope h1 mb-0 text-primary"></i>
                        </div>
                    </div>
                    <div class="p-2 mt-2">
                        <h6>Send Verification Link</h6>
                        <p class="font-size-12 text-muted">
                            Send Verification link to 
                            <span class="fw-bold">{{ list.email }}</span> for activation.
                        </p>
                        <div class="mt-4">
                            <button type="button" :disabled="form.processing" @click="sendMail()" class="btn btn-success w-md">Send Mail</button>
                        </div>
                    </div>
                </div>
                <div v-else class="mt-3 mb-2">
                    <div class="avatar-md mx-auto">
                        <div class="avatar-title rounded-circle bg-light">
                            <i class="bx bx-mail-send h1 mb-0 text-primary"></i>
                        </div>
                    </div>
                    <div class="p-2 mt-2">
                        <h5 class="text-success">Success!</h5>
                        <p class="font-size-12 text-muted">
                            Verification was sent to
                            <span class="fw-bold">{{ list.email }}</span> for activation.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </b-modal>
</template>
<script>
    export default {
        data() {
            return {
                currentUrl: window.location.origin,
                form: {},
                list: {},
                type: '',
                showModal: false,
                editable: false,
                status: false,
            }
        },

        methods: {
            show(type, data) {
                this.type = type;
                this.list = data;
                this.showModal = true;
            },

            hide() {
                this.showModal = false;
            },

            sendMail() {
                this.form = this.$inertia.form({
                    id: this.list.id,
                    type: this.type
                });
                this.form.put('/staffs/update',{
                    preserveScroll: true,
                    onSuccess: (response) => {
                        this.showModal = false;
                        this.form.editable = true;
                    }
                });
            },

             create(){
                axios.put(this.currentUrl + '/staffs/update', {
                    id: this.user.id,
                    is_active: (this.user.is_active == 1) ? 0 : 1
                })
                .then(response => {
                    this.$emit('info', response.data.data)
                })
                .catch(error => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                });
            },
        },
    }

</script>
<style>
    .multiselect__single {
        font-size: 11px;
    }

</style>
