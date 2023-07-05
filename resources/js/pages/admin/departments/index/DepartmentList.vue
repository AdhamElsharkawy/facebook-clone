<template>
    <Loading v-if="loading" />
    <DataTable
        ref="dt"
        :value="departments"
        v-model:selection="selectedDepartments"
        dataKey="id"
        :paginator="true"
        :rows="10"
        :filters="filters"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[5, 10, 25]"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} departments"
        responsiveLayout="scroll"
    >
        <template #header>
            <div
                class="flex flex-column md:flex-row md:justify-content-between md:align-items-center"
                :class="{ 'md:flex-row-reverse': $store.getters['isRtl'] }"
            >
                <h5 class="m-0">{{ $t("manage") + " " + "departments" }}</h5>
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
            field="name"
            :header="$t('name')"
            :sortable="true"
            headerStyle="width:14%; min-width:10rem;"
            :class="{ 'text-right': $store.getters['isRtl'] }"
        >
            <template #body="slotProps">
                <span class="p-column-title">Name</span>
                {{ slotProps.data.name }}
            </template>
        </Column>


<!--        <Column-->
<!--            field="department"-->
<!--            :header="$t('department')"-->
<!--            :sortable="true"-->
<!--            headerStyle="width:14%; min-width:14rem;"-->
<!--            :class="{ 'text-right': $store.getters['isRtl'] }"-->
<!--        >-->
<!--            <template #body="slotProps">-->
<!--                <span class="p-column-title">department</span>-->
<!--                {{ slotProps.data.department?.name }}-->
<!--            </template>-->
<!--        </Column>-->


        <Column
            field="action"
            :header="$t('actions')"
            headerStyle="min-width:10rem;display: flex; justify-content: center;"
            class="text-center"
        >
            <template #body="slotProps">
                <Button
                    icon="pi pi-pencil"
                    class="p-button-rounded p-button-success mx-2"
                    @click="editDepartment(slotProps.data)"
                />
                <Button
                    icon="pi pi-trash"
                    class="p-button-rounded p-button-warning mx-2"
                    @click="confirmDeleteDepartment(slotProps.data)"
                />
            </template>
        </Column>
    </DataTable>
    <Dialog
        v-model:visible="deleteDepartmentDialog"
        :style="{ width: '450px' }"
        header="Confirm"
        :modal="true"
    >
        <div class="flex align-items-center justify-content-center">
            <i
                class="pi pi-exclamation-triangle mr-3"
                style="font-size: 2rem"
            />
            <span v-if="department"
                >Are you sure you want to delete <b>{{ department.name }}</b
                >?</span
            >
        </div>
        <template #footer>
            <Button
                label="No"
                icon="pi pi-times"
                class="p-button-text"
                @click="deleteDepartmentDialog = false"
            />
            <Button
                label="Yes"
                icon="pi pi-check"
                class="p-button-text"
                @click="deleteDepartment"
            />
        </template>
    </Dialog>
</template>

<script>
import { FilterMatchMode } from "primevue/api";
import { useToast } from "primevue/usetoast";

export default {
    props: {
        currentDepartments: {
            type: Array,
            required: true,
        },

    }, //end of props

    emits: ["selectDepartments", "deleteDepartment", "editDepartment"],

    data() {
        return {
            toast: null,
            loading: false,
            departmentDialog: false,
            deleteDepartmentDialog: false,
            department: {},
            departments: this.currentDepartments,
            selectedDepartments: null,
            filters: {
                global: { value: null, matchMode: FilterMatchMode.CONTAINS },
            },
        };
    }, //end of data

    watch: {
        selectedDepartments(val) {
            this.$emit("selectDepartments", val);
        },
    }, //end of watch

    beforeMount() {
        this.initFilters();
        this.toast = useToast();
    }, //end of beforeMount

    methods: {
        confirmDeleteDepartment(department) {
            this.department = department;
            this.deleteDepartmentDialog = true;
        }, //end of confirmDeleteDepartment

        deleteDepartment() {
            this.loading = true;
            axios
                .delete("/api/admin/departments/" + this.department.id)
                .then((response) => {
                    this.toast.add({
                        severity: "success",
                        summary: "Successful",
                        detail: response.data.message,
                        life: 3000,
                    });
                    this.$emit("deleteDepartment");
                    this.deleteDepartmentDialog = false;
                    this.department = {};
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
                    this.deleteDepartmentDialog = false;
                });
        }, //end of deleteDepartment

        initFilters() {
            this.filters = {
                global: { value: null, matchMode: FilterMatchMode.CONTAINS },
            };
        }, //end of initFilters

        exportCSV() {
            this.$refs.dt.exportCSV();
        }, //end of exportCSV

        editDepartment(department) {
            this.$emit("editDepartment", department);
        }, //end of editDepartment
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
