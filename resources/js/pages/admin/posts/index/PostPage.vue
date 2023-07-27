<template>
    <Toast />
    <Loading v-if="loading" />
    <div class="grid" v-else>
        <div class="col-12">
            <div class="card">
                <post-list
                    ref="listPostComponent"
                    :currentPosts="currentPosts"
                    :rows="rows"
                    :totalPages="totalPages"
                    :currentPage="currentPage"
                    @selectPosts="selectPosts"
                    @editPost="editPost"
                    @pageChange="pageChange"
                    @deletePost="fill"
                ></post-list>

                <edit-post ref="editPostComponent"></edit-post>

                <Dialog
                    v-model:visible="deletePostsDialog"
                    :style="{ width: '450px' }"
                    header="Confirm"
                    :modal="true"
                >
                    <div class="flex align-items-center justify-content-center">
                        <i
                            class="pi pi-exclamation-triangle mr-3"
                            style="font-size: 2rem"
                        />
                        <span v-if="post"
                            >Are you sure you want to delete the selected
                            posts?</span
                        >
                    </div>
                    <template #footer>
                        <Button
                            label="No"
                            icon="pi pi-times"
                            class="p-button-text"
                            @click="deletePostsDialog = false"
                        />
                        <Button
                            label="Yes"
                            icon="pi pi-check"
                            class="p-button-text"
                            @click="deleteSelectedPosts"
                        />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>

<script>
import PostList from "./PostList.vue";
import EditPost from "../edit/EditPost.vue";
import { useToast } from "primevue/usetoast";

export default {
    components: { PostList, EditPost },
    data() {
        return {
            currentPosts: [],
            deletePostsDialog: false,
            selectedPosts: null,
            loading: false,
            isEmpty: false,
            errors: null,
            rows: 0,
            totalPages: 0,
            currentPage: 1,
            first_page_url: "",
            last_page: 0,
        };
    }, //end of data

    methods: {
        deleteSelectedPosts() {
            this.loading = true;
            axios
                .delete("/api/admin/posts/delete/all", {
                    data: {
                        posts: this.selectedPosts.map((val) => val.id),
                    },
                })
                .then((response) => {
                    this.currentPosts = this.currentPosts.filter(
                        (val) => !this.selectedPosts.includes(val)
                    );
                    this.selectedPosts = null;
                    this.toast.add({
                        severity: "success",
                        summary: "Successful",
                        detail: response.data.message,
                        life: 3000,
                    });
                })
                .catch((errors) => {
                    if (errors.response) {
                        this.toast.add({
                            severity: "error",
                            summary: "Error",
                            detail: errors.response.data.message,
                            life: 15000,
                        });
                    }
                })
                .then(() => {
                    this.loading = false;
                    this.deletePostsDialog = false;
                });
        }, //end of deleteSelectedPosts

        confirmDeleteSelected() {
            this.deletePostsDialog = true;
        }, //end of confirmDeleteSelected

        exportCSV() {
            this.$refs.listPostComponent.exportCSV();
        }, //end of exportCSV

        fill(currentPage) {
            this.loading = true;
            axios
                .get(`/api/admin/posts?page=${currentPage}`)
                // .get("/api/admin/posts?page=1")
                .then((response) => {
                    this.currentPosts = response.data.posts.data;
                    console.log(this.currentPosts);
                    this.posts = response.data.posts.data;
                    this.rows = response.data.posts.per_page;
                    this.totalPages = response.data.posts.last_page;
                    this.currentPage = response.data.posts.current_page;
                })
                .catch((errors) => {
                    this.error = errors.response.data;
                })
                .then(() => {
                    this.loading = false;
                }); //end of axios request
        }, //end of fill function

        selectPosts(selectedPosts) {
            this.selectedPosts = selectedPosts;
        }, //end of selectPosts

        editPost(post) {
            this.$refs.editPostComponent.openDialog(post);
        }, //end of editPost
        pageChange(currentPage) {
            this.fill(currentPage);
        }, //end of pageChange
    }, //end of methods

    beforeMount() {
        this.toast = useToast();
    }, //end of beforeMount

    created() {
        this.fill();
    }, //end of created
};
</script>
