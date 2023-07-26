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
                                :label="$t('new')"
                                icon="pi pi-plus"
                                class="p-button-success"
                                :class="{
                                    'mr-2': !$store.getters['isRtl'],
                                    'ml-2': $store.getters['isRtl'],
                                }"
                                @click="createNewDepartment"
                            />
                            <Button
                                :label="$t('delete')"
                                icon="pi pi-trash"
                                class="p-button-danger"
                                @click="confirmDeleteSelected"
                                :disabled="
                                    !selectedDepartments || !selectedDepartments.length
                                "
                            />
                        </div>
                    </template>

                    <template v-slot:end>
                        <Button
                            :label="$t('export')"
                            icon="pi pi-upload"
                            class="p-button-help"
                            @click="exportCSV($event)"
                        />
                    </template>
                </Toolbar>

                <department-list
                    ref="listDepartmentComponent"
                    :currentDepartments="currentDepartments"
                    @selectDepartments="selectDepartments"
                    @editDepartment="editDepartment"
                    @deleteDepartment="fill"
                ></department-list>

                <edit-department ref="editDepartmentComponent"></edit-department>

                <create-department
                    ref="createDepartmentComponent"
                    @departmentCreated="fill"
                ></create-department>

                <Dialog
                    v-model:visible="deleteDepartmentsDialog"
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
                            >Are you sure you want to delete the selected
                            departments?</span
                        >
                    </div>
                    <template #footer>
                        <Button
                            label="No"
                            icon="pi pi-times"
                            class="p-button-text"
                            @click="deleteDepartmentsDialog = false"
                        />
                        <Button
                            label="Yes"
                            icon="pi pi-check"
                            class="p-button-text"
                            @click="deleteSelectedDepartments"
                        />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>

<script>
import DepartmentList from "./DepartmentList.vue";
import EditDepartment from "../edit/EditDepartment.vue";
import CreateDepartment from "../create/CreateDepartment.vue";
import { useToast } from "primevue/usetoast";

export default {
    components: { DepartmentList, EditDepartment, CreateDepartment },
    data() {
        return {
            currentDepartments: [],
            deleteDepartmentsDialog: false,
            selectedDepartments: null,
            loading: false,
            isEmpty: false,
            errors: null,
        };
    }, //end of data

    methods: {
        createNewDepartment() {
            this.department = {};
            this.$refs.createDepartmentComponent.openDialog();
        }, //end of openNew

        deleteSelectedDepartments() {
            this.loading = true;
            axios
                .delete("/api/admin/departments/delete/all", {
                    data: {
                        departments: this.selectedDepartments.map((val) => val.id),
                    },
                })
                .then((response) => {
                    this.currentDepartments = this.currentDepartments.filter(
                        (val) => !this.selectedDepartments.includes(val)
                    );
                    this.selectedDepartments = null;
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
                    this.deleteDepartmentsDialog = false;
                });
        }, //end of deleteSelectedDepartments

        confirmDeleteSelected() {
            this.deleteDepartmentsDialog = true;
        }, //end of confirmDeleteSelected

        exportCSV() {
            this.$refs.listDepartmentComponent.exportCSV();
        }, //end of exportCSV

        fill() {
            this.loading = true;
            axios
                .get("/api/admin/departments")
                .then((response) => {
                    this.currentDepartments = response.data.departments;
                    this.departments = response.data.departments;
                })
                .catch((errors) => {
                    this.error = errors.response.data;
                })
                .then(() => {
                    this.loading = false;
                }); //end of axios request
        }, //end of fill function

        selectDepartments(selectedDepartments) {
            this.selectedDepartments = selectedDepartments;
        }, //end of selectDepartments

        editDepartment(department) {
            this.$refs.editDepartmentComponent.openDialog(department);
        }, //end of editDepartment
    }, //end of methods

    beforeMount() {
        this.toast = useToast();
    }, //end of beforeMount

    created() {
        this.fill();
    }, //end of created
};
</script>
