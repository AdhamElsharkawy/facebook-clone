<template>
    <Loading v-if="loading" />
    <DataTable
        ref="dt"
        :value="certifications"
        v-model:selection="selectedCertifications"
        dataKey="id"
        :filters="filters"
        responsiveLayout="scroll"
    >
        <template #header>
            <div
                class="flex flex-column md:flex-row md:justify-content-between md:align-items-center"
                :class="{ 'md:flex-row-reverse': $store.getters['isRtl'] }"
            >
                <h5 class="m-0">{{ $t("manage") + " " + "certifications" }}</h5>
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
            field="valid_until"
            header="valid_until"
            :sortable="true"
            headerStyle="width:14%; min-width:14rem;"
            :class="{ 'text-right': $store.getters['isRtl'] }"
        >
            <template #body="slotProps">
                <span class="p-column-title">Valid Until</span>
                {{ slotProps.data.valid_until }}
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
            field="confirmation_link"
            header="confirmation_link"
            :sortable="true"
            headerStyle="width:14%; min-width:14rem;"
            :class="{ 'text-right': $store.getters['isRtl'] }"
        >
            <template #body="slotProps">
                <span class="p-column-title">Confirmation Link</span>
                {{ slotProps.data.confirmation_link}}
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
                    @click="editCertification(slotProps.data)"
                />
                <Button
                    icon="pi pi-trash"
                    class="p-button-rounded p-button-warning mx-2"
                    @click="confirmDeleteCertification(slotProps.data)"
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
        v-model:visible="deleteCertificationDialog"
        :style="{ width: '450px' }"
        header="Confirm"
        :modal="true"
    >
        <div class="flex align-items-center justify-content-center">
            <i
                class="pi pi-exclamation-triangle mr-3"
                style="font-size: 2rem"
            />
            <span v-if="certification"
                >Are you sure you want to delete <b>{{ certification.name }}</b
                >?</span
            >
        </div>
        <template #footer>
            <Button
                label="No"
                icon="pi pi-times"
                class="p-button-text"
                @click="deleteCertificationDialog = false"
            />
            <Button
                label="Yes"
                icon="pi pi-check"
                class="p-button-text"
                @click="deleteCertification"
            />
        </template>
    </Dialog>
</template>

<script>
import { FilterMatchMode } from "primevue/api";
import { useToast } from "primevue/usetoast";

export default {
    props: {
        currentCertifications: {
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

    emits: ["selectCertifications", "deleteCertification", "editCertification","pageChange"],

    data() {
        return {
            toast: null,
            loading: false,
            certificationDialog: false,
            deleteCertificationDialog: false,
            certification: {},
            certifications: this.currentCertifications,
            selectedCertifications: null,
            filters: {
                global: { value: null, matchMode: FilterMatchMode.CONTAINS },
            },
        };
    }, //end of data

    watch: {
        selectedCertifications(val) {
            this.$emit("selectCertifications", val);
        },
    }, //end of watch

    beforeMount() {
        this.initFilters();
        this.toast = useToast();
    }, //end of beforeMount

    methods: {
        confirmDeleteCertification(certification) {
            this.certification = certification;
            this.deleteCertificationDialog = true;
        }, //end of confirmDeleteCertification

        onPageChange(pageNumber) {
            this.$emit("pageChange", pageNumber);
        }, //end of onPageChange

        deleteCertification() {
            this.loading = true;
            axios
                .delete("/api/admin/certifications/" + this.certification.id)
                .then((response) => {
                    this.toast.add({
                        severity: "success",
                        summary: "Successful",
                        detail: response.data.message,
                        life: 3000,
                    });
                    this.$emit("deleteCertification");
                    this.deleteCertificationDialog = false;
                    this.certification = {};
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
                    this.deleteCertificationDialog = false;
                });
        }, //end of deleteCertification

        initFilters() {
            this.filters = {
                global: { value: null, matchMode: FilterMatchMode.CONTAINS },
            };
        }, //end of initFilters

        exportCSV() {
            this.$refs.dt.exportCSV();
        }, //end of exportCSV

        editCertification(certification) {
            this.$emit("editCertification", certification);
        }, //end of editCertification
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
