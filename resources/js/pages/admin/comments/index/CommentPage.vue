<template>
    <Toast />
    <Loading v-if="loading" />
    <div class="grid" v-else>
        <div class="col-12">
            <div class="card">
                <Toolbar
                    class="mb-4"
                    :class="{
                        'flex flex-row-reverse': $store.getters['isRtl'],
                    }"
                >
                    <template v-slot:start>
                        <div
                            class="my-2"
                            :class="{
                                'flex flex-row-reverse':
                                    $store.getters['isRtl'],
                            }"
                        >
                            <Button
                                :label="$t('delete')"
                                icon="pi pi-trash"
                                class="p-button-danger"
                                @click="confirmDeleteSelected"
                                :disabled="
                                    !selectedComments || !selectedComments.length
                                "
                            />
                        </div>
                    </template>

                </Toolbar>

                <comment-list
                    ref="listCommentComponent"
                    :currentComments="currentComments"
                    :rows="rows"
                    :totalPages="totalPages"
                    :currentPage="currentPage"
                    @pageChange="pageChange"
                    @selectComments="selectComments"
                    @deleteComment="fill"
                ></comment-list>



                <Dialog
                    v-model:visible="deleteCommentsDialog"
                    :style="{ width: '450px' }"
                    header="Confirm"
                    :modal="true"
                >
                    <div class="flex align-items-center justify-content-center">
                        <i
                            class="pi pi-exclamation-triangle mr-3"
                            style="font-size: 2rem"
                        />
                        <span v-if="comment"
                            >Are you sure you want to delete the selected
                            comments?</span
                        >
                    </div>
                    <template #footer>
                        <Button
                            label="No"
                            icon="pi pi-times"
                            class="p-button-text"
                            @click="deleteCommentsDialog = false"
                        />
                        <Button
                            label="Yes"
                            icon="pi pi-check"
                            class="p-button-text"
                            @click="deleteSelectedComments"
                        />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>

<script>
import CommentList from "./CommentList.vue";
import { useToast } from "primevue/usetoast";

export default {
    components: { CommentList },
    data() {
        return {
            currentComments: [],
            deleteCommentsDialog: false,
            selectedComments: null,
            loading: false,
            isEmpty: false,
            errors: null,
            rows: 0,
            totalPages: 0,
            currentPage: 1,
        };
    }, //end of data

    methods: {
        deleteSelectedComments() {
            this.loading = true;
            axios
                .delete("/api/admin/comments/delete/all", {
                    data: {
                        comments: this.selectedComments.map((val) => val.id),
                    },
                })
                .then((response) => {
                    this.currentComments = this.currentComments.filter(
                        (val) => !this.selectedComments.includes(val)
                    );
                    this.selectedComments = null;
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
                    this.deleteCommentsDialog = false;
                });
        }, //end of deleteSelectedComments

        confirmDeleteSelected() {
            this.deleteCommentsDialog = true;
        }, //end of confirmDeleteSelected

        exportCSV() {
            this.$refs.listCommentComponent.exportCSV();
        }, //end of exportCSV

        fill(currentPage) {
            this.loading = true;
            axios
                .get(`/api/admin/comments?page=${currentPage}`)
                .then((response) => {
                    this.currentComments = response.data.comments.data;
                    // this.rows = response.data.comments.per_page;
                    this.totalPages = response.data.comments.last_page;
                    this.currentPage = response.data.comments.current_page;
                })
                .catch((errors) => {
                    this.error = errors.response.data;
                })
                .then(() => {
                    this.loading = false;
                }); //end of axios request
        }, //end of fill function
        pageChange(currentPage) {
            this.fill(currentPage);
        }, //end of pageChange

        selectComments(selectedComments) {
            this.selectedComments = selectedComments;
        }, //end of selectComments
    }, //end of methods

    beforeMount() {
        this.toast = useToast();
    }, //end of beforeMount

    created() {
        this.fill();
    }, //end of created
};
</script>
