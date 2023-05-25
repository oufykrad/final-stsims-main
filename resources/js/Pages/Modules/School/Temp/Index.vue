<template>

    <Head title="Schools" />
    <PageHeader :title="title" :items="items" />
    <div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1">
        <div class="file-manager-content w-100 p-4 pb-0" style="height: calc(100vh - 180px)" ref="box">
            
        </div>
    </div>
</template>
<script>
import PageHeader from "@/Shared/Components/PageHeader.vue";
import Pagination from "@/Shared/Components/Pagination.vue";
export default {
    components: { PageHeader, Pagination },
    props: ['dropdowns'],
    data() {
        return {
            currentUrl: window.location.origin,
            title: "List of Schools",
            items: [{text: "List",href: "/"}, {text: "School",active: true}],
            lists: [],
            meta: {},
            links: {},
            keyword: null
        };
    },
    computed: {
        terms : function() {
            return this.dropdowns.filter(x => x.classification === "Term Type");
        },
        classes : function() {
            return this.dropdowns.filter(x => x.classification === "Class");
        },
        gradings : function() {
            return this.dropdowns.filter(x => x.classification === "Grading System");
        },
    },
    watch: {
        keyword(newVal){
            this.checkSearchStr(newVal)
        }
    },
    created(){
        this.fetch();
    },
    methods: {
        checkSearchStr: _.debounce(function(string) {
            this.fetch();
        }, 300),
        fetch(page_url) {
            page_url = page_url || '/schools';
            axios.get(page_url, {
                params: {
                    lists: true,
                    counts: ((window.innerHeight-350)/56),
                    keyword: this.keyword
                }
            })
            .then(response => {
                this.lists = response.data.data;
                this.meta = response.data.meta;
                this.links = response.data.links;
            })
            .catch(err => console.log(err));
        },
        percentage(data) {
            return Math.floor((data / this.total) * 100) + '%';
        },
        importModal() {
            this.$refs.import.show();
        },
        create(){
            this.$refs.create.show();
        },
        edit(data){
            this.$refs.create.edit(data);
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
