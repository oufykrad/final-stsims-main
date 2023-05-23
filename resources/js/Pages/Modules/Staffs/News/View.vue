<template>
    <b-modal v-model="showModal" hide-footer title="View Credentials" header-class="p-3 bg-light" class="v-modal-custom" modal-class="zoomIn" centered no-close-on-backdrop>    
        <b-form class="customform mb-2">
            <div class="row customerform">
                <div class="col-md-12 mt-4">
                    <div class="form-group">
                        <label>Temp Password:</label>
                        <input type="text" class="form-control" v-model="user.temp_password" readonly>
                    </div>
                </div>  
                <div class="col-md-12">
                    <hr class="text-muted"/>
                </div>
                <div class="col-md-12 mt-2">
                    <div class="form-group">
                        <label>Api Key:</label>
                        <div class="position-relative auth-pass-inputgroup mb-3">
                                <input :type="(!showPassword) ? 'password' : 'text'" v-model="token" class="form-control pe-5" placeholder="Generate new Token"
                                id="password-input" />
                                <b-button @click="toggle" variant="link" class="position-absolute end-0 top-0 text-decoration-none text-muted"
                                type="button" id="password-addon">
                                <i class="ri-eye-fill align-middle"></i>
                                </b-button>
                                <div class="invalid-feedback">
                                <span></span>
                                </div>
                            </div>
                    </div>
                </div>
                
                <div class="col-md-12 mt-4">
                    <div class="d-grid gap-2" >
                        <b-button @click="create()" type="button" variant="success">Generate Key</b-button>
                        <b-button @click="revoke()" type="button" variant="danger">Revoke Key</b-button>
                    </div>
                </div>
            </div>
        </b-form>
    </b-modal>
</template>
<script>
export default {
    data(){
        return {
            currentUrl: window.location.origin,
            user: '',
            token: null,
            showModal: false,
            showPassword: false
        }
    },

    methods : {
        show(data) {
            this.user = data;
            this.showModal = true;
        },

        hide(){
            this.showModal = false;
        },

        create(){
            axios.put(this.currentUrl + '/staffs/update', {
                id: this.user.id,
                type: 'token'
            })
            .then(response => {
                this.token = response.data;
            })
            .catch(error => {
                if (error.response.status == 422) {
                    this.errors = error.response.data.errors;
                }
            });
        },

        revoke(){
             this.form = this.$inertia.form({
                id: this.user.id,
                type: 'revoke',
                editable: true
            })

            this.form.put('/staffs/update',{
                preserveScroll: true,
                onSuccess: (response) => {
                    this.hide();
                },
            });
        },

        toggle(){
            if(this.showPassword){
                this.showPassword = false;
            }else{
                this.showPassword = true;
            }
        }
    }
}
</script>
