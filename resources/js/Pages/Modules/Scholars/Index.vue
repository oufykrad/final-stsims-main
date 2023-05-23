<template>

    <Head title="Scholars" />
    <PageHeader :title="title" :items="items" />
    <div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1">

        <div class="file-manager-sidebar">
            <div class="p-4 d-flex flex-column h-100">

                <div class="table-responsive">
                    <table class="table table-borderless table-sm table-centered align-middle table-nowrap">
                        <tbody class="border-0">
                            <!-- <tr>
                                <td>
                                    <h4 class="text-truncate fs-14 fs-medium mb-0"><i
                                            class="ri-stop-fill align-middle fs-18 text-success me-2"></i>Registered Scholar
                                    </h4>
                                </td>
                                <td class="text-end"><p class="text-success mb-0">{{registered}}</p></td>
                            </tr>
                            <tr>
                                <td>
                                    <h4 class="text-truncate fs-14 fs-medium mb-0"><i
                                            class="ri-stop-fill align-middle fs-18 text-warning me-2"></i>Incomplete Details
                                    </h4>
                                </td>
                                <td class="text-end"><p class="text-warning mb-0">{{incomplete}}</p></td>
                            </tr>
                            <tr>
                                <td>
                                    <h4 class="text-truncate fs-14 fs-medium mb-0"><i
                                            class="ri-stop-fill align-middle fs-18 text-info me-2"></i>Undergrad
                                    </h4>
                                </td>
                                <td class="text-end">
                                    <p class="text-muted mb-0">{{undergrad}}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h4 class="text-truncate fs-14 fs-medium mb-0"><i
                                            class="ri-stop-fill align-middle fs-18 text-info me-2"></i>JLSS
                                    </h4>
                                </td>
                                <td class="text-end">
                                    <p class="text-muted mb-0">{{jlss}}</p>
                                </td>
                            </tr> -->
                            <tr>
                                <td>
                                    <h4 class="text-truncate fs-14 fs-medium mb-0"><i
                                            class="ri-stop-fill align-middle fs-18 text-primary me-2"></i>Total Scholars
                                    </h4>
                                </td>
                                <td class="text-end">
                                    <p class="text-primary fw-bold mb-0">{{total}}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h4 class="text-truncate fs-14 fs-medium mb-0"><i class="ri-stop-fill align-middle fs-18 text-success me-2"></i>Ongoing Scholars
                                    </h4>
                                </td>
                                <td class="text-end">
                                    <p class="text-success fw-bold mb-0">{{ongoing}}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <hr class="text-muted mt-n1 mb-4"/>
                <ul class="list-group mb-4" style="cursor: pointer;">
                    <li class="list-group-item" @click="print('graduated')"><i class="bx bxs-printer float-end fs-20 text-light align-middle me-2"></i><i class="bx fs-18 bxs-award text-warning align-middle me-2"></i>Graduates <span class="text-muted fs-11">(w/ Honor)</span> </li>
                    <li class="list-group-item" @click="print('graduates')"><i class="bx bxs-printer float-end fs-20 text-light align-middle me-2"></i><i class="bx fs-18 bxs-graduation text-warning align-middle me-2"></i>List of Graduates</li>
                    <li class="list-group-item" @click="print('scholars')"><i class="bx bxs-printer float-end fs-20 text-light align-middle me-2"></i><i class="ri-team-fill fs-18 text-warning align-middle me-2"></i>List of Scholars</li>
                </ul>

                
                

                <!-- <ul class="list-group list-group-flush border-dashed mb-0 mt-3 pt-2" data-simplebar style="height: 300px;">
                     <li class="list-group-item px-0" v-for="(status,index) in statuses" v-bind:key="index">
                        <div class="d-flex">
                            <div class="flex-grow-1 ms-2">
                                <p class="fs-12 mb-0 text-muted">{{ status.name }}</p>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                <p class="text-success fs-12 mb-0"> {{percentage(status.status_count)}} ({{status.status_count }})</p>
                            </div>
                        </div>
                    </li>
                </ul> -->
                <!-- <ul class="list-group list-group-flush" data-simplebar style="height: 380px;" v-if="statuses">
                    <li class="list-group-item" v-for="(status,index) in statuses" v-bind:key="index">
                        <div class="mb-n2">
                            <h5 class="fs-11">
                                {{ status.name }} <span class="float-end"> {{percentage(status.status_count)}}
                                    ({{status.status_count }})</span>
                            </h5>
                        </div>
                    </li>
                </ul> -->

                <div class="mt-auto">
                    <b-row class="g-1">
                        <b-col lg="6">
                            <button class="btn btn-soft-primary btn-sm w-100" type="button">
                                <div class="btn-content"> Sync </div>
                            </button>
                        </b-col>
                        <b-col lg="6">
                            <button @click="importModal()" class="btn btn-primary btn-sm w-100" type="button">
                                <div class="btn-content"> Import </div>
                            </button>
                        </b-col>
                    </b-row>
                </div>
            </div>
        </div>

        <div class="file-manager-content w-100 p-4 pb-0" style="height: calc(100vh - 180px)" ref="box">
            <Lists :regions="regions" :programs="programs" :dropdowns="dropdowns" />
        </div>
    </div>

    <Import ref="import" />
    <Print :dropdowns="dropdowns" :programs="programs" ref="print"/>
</template>
<script>
import Lists from './Lists.vue';
import Import from './Modals/Import.vue';
import Print from './Modals/Print.vue';
import PageHeader from "@/Shared/Components/PageHeader.vue";
export default {
    props: ['statuses', 'regions', 'programs', 'dropdowns'],
    components: { PageHeader, Import, Lists, Print },
    page: {
        title: "List of Staffs",
        meta: [{
            name: "description"
        }],
    },
    data() {
        return {
            currentUrl: window.location.origin,
            title: "List of Scholars",
            items: [{
                text: "Lists",
                href: "/",
            }, {
                text: "Scholar",
                active: true,
            }, ],
            statuses: [],
            total: '',
            incomplete: '',
            undergrad: '',
            jlss: '',
            registered: '',
            ongoing: ''
        };
    },
    created() {
        this.fetchStatus();
    },
    methods: {
        fetchStatus() {
            axios.get(this.currentUrl + '/insights', {
                    params: {
                        search: 'lists'
                    }
                })
                .then(response => {
                    this.statuses = response.data.statuses;
                    this.total = response.data.total;
                    this.ongoing = response.data.ongoing;
                    this.incomplete = response.data.incomplete;
                    this.undergrad = response.data.undergrad;
                    this.jlss = response.data.jlss;
                    this.registered = response.data.registered;
                })
                .catch(err => console.log(err));
        },
        percentage(data) {
            return Math.floor((data / this.total) * 100) + '%';
        },
        importModal() {
            this.$refs.import.show();
        },
        print(type){
            this.$refs.print.set(type);
        }
    }
}
</script>
<style>
    .file-manager-sidebar {
        min-width: 450px;
        max-width: 450px;
        height: calc(100vh - 180px);
    }

</style>
