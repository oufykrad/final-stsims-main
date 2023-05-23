<template>
    <b-modal v-model="showModal" hide-footer title="Import Course" header-class="p-3 bg-light" size="lg" class="v-modal-custom" modal-class="zoomIn" centered no-close-on-backdrop>
        
        <b-form action="#" id="editform" class="tablelist-form" autocomplete="off">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="p-2">
                        <div class="text-center">

                            <div class="avatar-md mx-auto">
                                <div class="avatar-title rounded-circle bg-light">
                                    <i class="bx bxs-file h1 mb-0 text-primary"></i>
                                </div>
                            </div>
                            <div class="p-2 mt-4">
                                <form @submit.prevent="preview" enctype="multipart/form-data">
                                    <h5>Import using Excel</h5>
                                    <p class="text-muted">Please click preview to generate the entry</p>
                                    <input class="mt-2 mb-4" type="file" @change="uploadFieldChange">
                                    <!-- <span v-if="errors['files.'+0]" class="haveerror">(required)</span> -->
                                    
                                    <br />
                                    <span v-if="isLoading"><i class='bx bx-loader-circle bx-spin mt-2'></i><span class="text-muted ms-1 mt-n4">Loading ... </span></span>
                                    <center v-if="users.length > 0"><span class="text-muted mb-4">Are you sure you want to import the <span class="font-weight-bold text-danger"> {{ users.length }}</span> courses?</span></center>
                                    <br />

                                    <ul class="align-center" v-if="result">
                                        <li class="list-inline-item d-non d-sm-inline-block me-4">     
                                            <i class='bx bxs-circle text-success'></i> <span class="text-muted font-size-11">Success <span v-if="success != ''">({{success}})</span></span>
                                        </li>
                                        <li class="list-inline-item d-non d-sm-inline-block me-4">     
                                            <i class='bx bxs-circle text-danger'></i> <span class="text-muted font-size-11">Failed <span v-if="failed != ''">({{failed}})</span></span>
                                        </li>
                                        <li class="list-inline-item d-non d-sm-inline-block" style="margin-right: 100px;">     
                                            <i class='bx bxs-circle text-warning'></i> <span class="text-muted font-size-11">Duplicate <span v-if="duplicate != ''">({{duplicate}})</span></span>
                                        </li>
                                    </ul>

                                    <div class="mt-4">
                                        <button type="submit" :disabled="users.length > 0" class="btn btn-light w-lg me-1">Preview</button>
                                        <button @click="sync" type="button" :disabled="users.length == 0" class="btn btn-primary w-lg">Import</button>
                                    </div>
                                </form>
                            </div>
                        </div>
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
            showModal: false,
            users: [],
            entry: '',
            success: 0,
            failed: 0,
            duplicate: 0,
            isLoading: false,
            result: false
        }
    },
    methods : {
        show(){
            this.showModal = true;
        },
        uploadFieldChange(e) {
            e.preventDefault();
            var file = e.target.files || e.dataTransfer.files;
            this.entry = file;
        },
        preview(){
            this.isLoading = true;
            let data = new FormData();
            data.append('import_file', this.entry[0]);
            data.append('type', 'preview');

            axios.post(this.currentUrl + '/courses', data)
            .then(response => {
                this.users = response.data;
                this.isLoading = false;
            })
            .catch(error => {
                if (error.response.status == 422) {
                    this.errors = error.response.data.errors;
                    this.isLoading = false;
                }
            });
        },
        sync(){
            this.isLoading = true;
            this.qualifiers = this.users;

            axios.post(this.currentUrl + '/courses', {
                lists: this.qualifiers,
                type: 'import'
            })
            .then(response => {
                this.result = true;
                this.success = response.success;
                this.failed = response.failed;
                this.duplicate = response.duplicate;

                this.isLoading = false;
                this.showModal = false;
            })
            .catch(error => {
                if (error.response.status == 422) {
                    this.errors = error.response.data.errors;
                    this.isLoading = false;
                }
            });
        }
    }
}
</script>
