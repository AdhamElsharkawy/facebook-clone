<template>
    <Loading v-if="loading" />
    <div class="card">
        <DataView :value="currentPosts">
            <template #list="slotProps">
                <div class="col-12">
                    <div
                        class="flex flex-column xl:flex-row xl:align-items-start p-4 gap-4"
                    >
                        <div v-if="slotProps.data.images_paths.length > 0">
                            <img
                                v-for="img_path in slotProps.data.images_paths"
                                :key="img_path"
                                class="w-9 sm:w-16rem xl:w-10rem shadow-2 mx-auto border-round inline-block"
                                style="width: 140px; height: 140px"
                                :src="img_path"
                                :alt="slotProps.data.name"
                            />
                        </div>
                        <div
                            class="flex flex-column sm:flex-row justify-content-between align-items-center xl:align-items-start flex-1 gap-4"
                        >
                            <div
                                class="flex flex-column align-items-center sm:align-items-start gap-3"
                            >
                                <div class="text-2xl font-bold text-900">
                                    ID : {{ slotProps.data.id }}
                                </div>
                                <div class="text-2xl font-bold text-900">
                                    Thread : {{ slotProps.data.thread }}
                                </div>
                                <!--                                <p>Total Likes : {{ slotProps.data.likes_count }}</p>-->
                                <!--                                <p>Total celebrates : {{ slotProps.data.celebrate_count }}</p>-->
                                <!--                                <p>Total loves : {{ slotProps.data.loves_count }}</p>-->
                                <p>
                                    Total Reactions :
                                    {{ slotProps.data.total_reactions }}
                                </p>
                                <p v-if="slotProps.data.polls.length > 0">
                                    <strong>Poll Caption :</strong>
                                    <br />
                                    {{ slotProps.data.poll_caption }}
                                </p>
                                <div class="flex align-items-center gap-3">
                                    <span class="flex align-items-center gap-2">
                                        <i class="pi pi-tag"></i>
                                        <span class="font-semibold">
                                            User :
                                            {{ slotProps.data.user.name }}</span
                                        >
                                    </span>
                                </div>
                            </div>
                            <div
                                class="flex sm:flex-column align-items-center sm:align-items-end gap-3 sm:gap-2"
                            >
                                <Button
                                    icon="pi pi-pencil"
                                    class="p-button-rounded p-button-success mx-2"
                                    @click="editPost(slotProps.data)"
                                />
                                <Button
                                    icon="pi pi-trash"
                                    class="p-button-rounded p-button-warning mx-2"
                                    @click="confirmDeletePost(slotProps.data)"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </DataView>

        <div class="flex flex-row justify-content-center mt-5">
            <button
                class="p-2"
                :class="currentPage === 1 ? 'p-disabled' : ''"
                @click="onPageChange(1)"
            >
                <Icon icon="mingcute:arrows-left-line" />
            </button>
            <button
                class="p-2"
                :class="currentPage === 1 ? 'p-disabled' : ''"
                @click="onPageChange(currentPage - 1)"
            >
                <Icon icon="iconamoon:arrow-left-2-duotone" width="16" />
            </button>
            <span class="p-2">Page {{ currentPage }} of {{ totalPages }}</span>
            <button
                class="p-2"
                :class="currentPage === totalPages ? 'p-disabled' : ''"
                @click="onPageChange(currentPage + 1)"
            >
                <Icon icon="iconamoon:arrow-right-2-duotone" width="16" />
            </button>
            <button
                class="p-2"
                :class="currentPage === totalPages ? 'p-disabled' : ''"
                @click="onPageChange(totalPages)"
            >
                <Icon icon="mingcute:arrows-right-line" />
            </button>
        </div>

        <Dialog
            v-model:visible="deletePostDialog"
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
                    >Are you sure you want to delete <b>{{ post.name }}</b
                    >?</span
                >
            </div>
            <template #footer>
                <Button
                    label="No"
                    icon="pi pi-times"
                    class="p-button-text"
                    @click="deletePostDialog = false"
                />
                <Button
                    label="Yes"
                    icon="pi pi-check"
                    class="p-button-text"
                    @click="deletePost"
                />
            </template>
        </Dialog>
    </div>
</template>

<script>
import { FilterMatchMode } from "primevue/api";
import { useToast } from "primevue/usetoast";
import { data } from "autoprefixer";

export default {
    computed: {
        data() {
            return data;
        },
    },
    props: {
        currentPosts: {
            type: Array,
            required: true,
        },
        currentPage: {
            type: Number,
            required: true,
        },
        rows: {
            type: Number,
            required: true,
        },
        totalPages: {
            type: Number,
            required: true,
        },
    }, //end of props

    emits: ["selectPosts", "deletePost", "editPost", "pageChange"],
    data() {
        return {
            toast: null,
            loading: false,
            postDialog: false,
            deletePostDialog: false,
            post: {},
            // posts: this.currentPosts,
            selectedPosts: null,
            filters: {
                global: { value: null, matchMode: FilterMatchMode.CONTAINS },
            },
        };
    }, //end of data

    watch: {
        selectedPosts(val) {
            this.$emit("selectPosts", val);
        },
    }, //end of watch

    methods: {
        onPageChange(pageNumber) {
            this.$emit("pageChange", pageNumber);
        },
        confirmDeletePost(post) {
            this.post = post;
            this.deletePostDialog = true;
        }, //end of confirmDeletePost

        deletePost() {
            this.loading = true;
            axios
                .delete("/api/admin/posts/" + this.post.id)
                .then((response) => {
                    this.toast.add({
                        severity: "success",
                        summary: "Successful",
                        detail: response.data.message,
                        life: 3000,
                    });
                    this.$emit("deletePost");
                    this.deletePostDialog = false;
                    this.post = {};
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
                    this.deletePostDialog = false;
                });
        }, //end of deletePost

        initFilters() {
            this.filters = {
                global: { value: null, matchMode: FilterMatchMode.CONTAINS },
            };
        }, //end of initFilters

        exportCSV() {
            this.$refs.dt.exportCSV();
        }, //end of exportCSV

        editPost(post) {
            this.$emit("editPost", post);
        }, //end of editPost
    }, //end of methods

    beforeMount() {
        this.initFilters();
        this.toast = useToast();
    }, //end of beforeMount
};
</script>

<style scoped lang="scss">
@import "../../../../assets/demo/styles/badges.scss";
</style>

<style lang="scss">
.text-right {
    .p-datatable {
        .p-column-header-content {
            display: flex;
            gap: 0.5rem;
        }
    }
    table {
        direction: rtl;
    }
}
</style>
