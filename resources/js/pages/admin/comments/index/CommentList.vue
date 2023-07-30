<template>
    <Loading v-if="loading" />
    <DataTable
        ref="dt"
        :value="comments"
        v-model:selection="selectedComments"
        dataKey="id"
        :filters="filters"
        responsiveLayout="scroll"
    >
        <template #header>
            <div
                class="flex flex-column md:flex-row md:justify-content-between md:align-items-center"
                :class="{ 'md:flex-row-reverse': $store.getters['isRtl'] }"
            >
                <h5 class="m-0">{{ $t("manage") + " " + "comments" }}</h5>
                <span class="block mt-2 md:mt-0 p-input-icon-left">
                    <i class="pi pi-search" />
                    <InputText
                        v-model="filters['global'].value"
                        :placeholder="$t('search')"
                        :class="{ 'text-right': $store.getters['isRtl'] }"
                    />
                </span>
            </div>
        </template>

        <Column
            selectionMode="multiple"
            headerStyle="width: 3rem"
            :class="{ 'text-right': $store.getters['isRtl'] }"
        ></Column>

        <Column
            field="thread"
            header="Comment"
            :sortable="true"
            headerStyle="width:14%; min-width:10rem;"
            :class="{ 'text-right': $store.getters['isRtl'] }"
        >
            <template #body="slotProps">
                <span class="p-column-title">Comment</span>
                {{ slotProps.data.thread }}
            </template>
        </Column>
        <Column
            field="user.name"
            header="user"
            :sortable="true"
            headerStyle="width:14%; min-width:10rem;"
            :class="{ 'text-right': $store.getters['isRtl'] }"
        >
            <template #body="slotProps">
                <span class="p-column-title">user</span>
                {{ slotProps.data.user.name}}
            </template>
        </Column>

        <Column
            field="post.thread"
            header="Post_thread"
            :sortable="true"
            headerStyle="width:14%; min-width:10rem;"
            :class="{ 'text-right': $store.getters['isRtl'] }"
        >
            <template #body="slotProps">
                <span class="p-column-title">Post_thread</span>
                {{ slotProps.data.post.thread }}
            </template>
        </Column>
        <Column
            field="post.id"
            header="Post_id"
            :sortable="true"
            headerStyle="width:14%; min-width:10rem;"
            :class="{ 'text-right': $store.getters['isRtl'] }"
        >
            <template #body="slotProps">
                <span class="p-column-title">post_id</span>
                {{ slotProps.data.post.id }}
            </template>
        </Column>
        <Column
            field="post.user.name"
            header="User_name_of_Post"
            :sortable="true"
            headerStyle="width:14%; min-width:10rem;"
            :class="{ 'text-right': $store.getters['isRtl'] }"
        >
            <template #body="slotProps">
                <span class="p-column-title">User of Post</span>
                {{ slotProps.data.post.user.name }}
            </template>
        </Column>

        <Column
            field="action"
            header="actions"
            headerStyle="min-width:10rem;display: flex; justify-content: center;"
            class="text-center"
        >
            <template #body="slotProps">
                <Button
                    icon="pi pi-trash"
                    class="p-button-rounded p-button-warning mx-2"
                    @click="confirmDeleteComment(slotProps.data)"
                />
            </template>
        </Column>
    </DataTable>

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
        v-model:visible="deleteCommentDialog"
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
                >Are you sure you want to delete <b>{{ comment.name }}</b
                >?</span
            >
        </div>
        <template #footer>
            <Button
                label="No"
                icon="pi pi-times"
                class="p-button-text"
                @click="deleteCommentDialog = false"
            />
            <Button
                label="Yes"
                icon="pi pi-check"
                class="p-button-text"
                @click="deleteComment"
            />
        </template>
    </Dialog>
</template>

<script>
import { FilterMatchMode } from "primevue/api";
import { useToast } from "primevue/usetoast";

export default {
    props: {
        currentComments: {
            type: Array,
            required: true,
        },
        currentPage: {
            type: Number,
            required: true,
        },
        totalPages: {
            type: Number,
            required: true,
        },
    }, //end of props

    emits: ["selectComments", "deleteComment", "pageChange"],

    data() {
        return {
            toast: null,
            loading: false,
            commentDialog: false,
            deleteCommentDialog: false,
            comment: {},
            comments: this.currentComments,
            selectedComments: null,
            filters: {
                global: { value: null, matchMode: FilterMatchMode.CONTAINS },
            },
        };
    }, //end of data

    watch: {
        selectedComments(val) {
            this.$emit("selectComments", val);
        },
    }, //end of watch

    beforeMount() {
        this.initFilters();
        this.toast = useToast();
    }, //end of beforeMount

    methods: {
        onPageChange(pageNumber) {
            this.$emit("pageChange", pageNumber);
        }, //end of onPageChange

        confirmDeleteComment(comment) {
            this.comment = comment;
            this.deleteCommentDialog = true;
        }, //end of confirmDeleteComment

        deleteComment() {
            this.loading = true;
            axios
                .delete("/api/admin/comments/" + this.comment.id)
                .then((response) => {
                    this.toast.add({
                        severity: "success",
                        summary: "Successful",
                        detail: response.data.message,
                        life: 3000,
                    });
                    this.$emit("deleteComment");
                    this.deleteCommentDialog = false;
                    this.comment = {};
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
                    this.deleteCommentDialog = false;
                });
        }, //end of deleteComment

        initFilters() {
            this.filters = {
                global: { value: null, matchMode: FilterMatchMode.CONTAINS },
            };
        }, //end of initFilters

        exportCSV() {
            this.$refs.dt.exportCSV();
        }, //end of exportCSV
    }, //end of methods
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
