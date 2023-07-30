<template>
    <Loading v-if="loading" />
    <DataTable
        ref="dt"
        :value="educations"
        v-model:selection="selectedEducations"
        dataKey="id"
        :filters="filters"
        responsiveLayout="scroll"
    >
        <template #header>
            <div
                class="flex flex-column md:flex-row md:justify-content-between md:align-items-center"
                :class="{ 'md:flex-row-reverse': $store.getters['isRtl'] }"
            >
                <h5 class="m-0">{{ $t("manage") + " " + "educations" }}</h5>
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
            field="degree"
            header="degree"
            :sortable="true"
            headerStyle="width:14%; min-width:10rem;"
            :class="{ 'text-right': $store.getters['isRtl'] }"
        >
            <template #body="slotProps">
                <span class="p-column-title">Degree</span>
              {{ slotProps.data.degree }}
            </template>
        </Column>
        <Column
            field="major"
            header="major"
            :sortable="true"
            headerStyle="width:16%; min-width:10rem;"
            :class="{ 'text-right': $store.getters['isRtl'] }"
        >
            <template #body="slotProps">
                <span class="p-column-title">Major</span>
               <div class="major">{{ slotProps.data.major }}</div>
            </template>
        </Column>

        <Column
            field="start_date"
            header="start_date"
            :sortable="true"
            headerStyle="width:14%; min-width:14rem;"
            :class="{ 'text-right': $store.getters['isRtl'] }"
        >
            <template #body="slotProps">
                <span class="p-column-title">Start Date</span>
                {{ slotProps.data.start_date }}
            </template>
        </Column>
        <Column
            field="end_date"
            header="end_date"
            :sortable="true"
            headerStyle="width:14%; min-width:14rem;"
            :class="{ 'text-right': $store.getters['isRtl'] }"
        >
            <template #body="slotProps">
                <span class="p-column-title">End Date</span>
                {{ slotProps.data.end_date }}
            </template>
        </Column>

        <Column
            field="is_current"
            header="is_current"
            :sortable="true"
            headerStyle="width:14%; min-width:14rem;"
            :class="{ 'text-right': $store.getters['isRtl'] }"
        >
            <template #body="slotProps">
                <span class="p-column-title">Is Current ?</span>
                {{ slotProps.data.is_current}}
            </template>
        </Column>

        <Column
            field="location"
            header="location"
            :sortable="true"
            headerStyle="width:14%; min-width:14rem;"
            :class="{ 'text-right': $store.getters['isRtl'] }"
        >
            <template #body="slotProps">
                <span class="p-column-title">Location</span>
                {{ slotProps.data.location}}
            </template>
        </Column>

        <Column
            field="college.name"
            header="college"
            :sortable="true"
            headerStyle="width:14%; min-width:14rem;"
            :class="{ 'text-right': $store.getters['isRtl'] }"
        >
            <template #body="slotProps">
                <span class="p-column-title">college</span>
                {{ slotProps.data.college?.name }}
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
                    icon="pi pi-pencil"
                    class="p-button-rounded p-button-success mx-2"
                    @click="editEducation(slotProps.data)"
                />
                <Button
                    icon="pi pi-trash"
                    class="p-button-rounded p-button-warning mx-2"
                    @click="confirmDeleteEducation(slotProps.data)"
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
        v-model:visible="deleteEducationDialog"
        :style="{ width: '450px' }"
        header="Confirm"
        :modal="true"
    >
        <div class="flex align-items-center justify-content-center">
            <i
                class="pi pi-exclamation-triangle mr-3"
                style="font-size: 2rem"
            />
            <span v-if="education"
                >Are you sure you want to delete <b>{{ education.name }}</b
                >?</span
            >
        </div>
        <template #footer>
            <Button
                label="No"
                icon="pi pi-times"
                class="p-button-text"
                @click="deleteEducationDialog = false"
            />
            <Button
                label="Yes"
                icon="pi pi-check"
                class="p-button-text"
                @click="deleteEducation"
            />
        </template>
    </Dialog>
</template>

<script>
import { FilterMatchMode } from "primevue/api";
import { useToast } from "primevue/usetoast";

export default {
    props: {
        currentEducations: {
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

    emits: ["selectEducations", "deleteEducation", "editEducation", "pageChange"],

    data() {
        return {
            toast: null,
            loading: false,
            educationDialog: false,
            deleteEducationDialog: false,
            education: {},
            educations: this.currentEducations,
            selectedEducations: null,
            filters: {
                global: { value: null, matchMode: FilterMatchMode.CONTAINS },
            },
        };
    }, //end of data

    watch: {
        selectedEducations(val) {
            this.$emit("selectEducations", val);
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
        confirmDeleteEducation(education) {
            this.education = education;
            this.deleteEducationDialog = true;
        }, //end of confirmDeleteEducation

        deleteEducation() {
            this.loading = true;
            axios
                .delete("/api/admin/educations/" + this.education.id)
                .then((response) => {
                    this.toast.add({
                        severity: "success",
                        summary: "Successful",
                        detail: response.data.message,
                        life: 3000,
                    });
                    this.$emit("deleteEducation");
                    this.deleteEducationDialog = false;
                    this.education = {};
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
                    this.deleteEducationDialog = false;
                });
        }, //end of deleteEducation

        initFilters() {
            this.filters = {
                global: { value: null, matchMode: FilterMatchMode.CONTAINS },
            };
        }, //end of initFilters

        exportCSV() {
            this.$refs.dt.exportCSV();
        }, //end of exportCSV

        editEducation(education) {
            this.$emit("editEducation", education);
        }, //end of editEducation
    }, //end of methods
};
</script>

<style scoped lang="scss">
.description{
        overflow: hidden;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 4;
        white-space: pre-wrap;
}
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
