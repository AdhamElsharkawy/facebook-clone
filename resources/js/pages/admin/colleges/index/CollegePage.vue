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
                                @click="createNewCollege"
                            />
                            <Button
                                :label="$t('delete')"
                                icon="pi pi-trash"
                                class="p-button-danger"
                                @click="confirmDeleteSelected"
                                :disabled="
                                    !selectedColleges || !selectedColleges.length
                                "
                            />
                        </div>
                    </template>

                    <template v-slot:end>
                        <Button
                            :label="$t('export')"
                            icon="pi pi-upload"
                            class="p-button-help"
                            @click="exportCSV($college)"
                        />
                    </template>
                </Toolbar>

                <college-list
                    ref="listCollegeComponent"
                    :currentColleges="currentColleges"
                    @selectColleges="selectColleges"
                    @editCollege="editCollege"
                    @deleteCollege="fill"
                ></college-list>

                <edit-college ref="editCollegeComponent" ></edit-college>

                <create-college
                    ref="createCollegeComponent"
                    @collegeCreated="fill"
                ></create-college>

                <Dialog
                    v-model:visible="deleteCollegesDialog"
                    :style="{ width: '450px' }"
                    header="Confirm"
                    :modal="true"
                >
                    <div class="flex align-items-center justify-content-center">
                        <i
                            class="pi pi-exclamation-triangle mr-3"
                            style="font-size: 2rem"
                        />
                        <span v-if="college"
                            >Are you sure you want to delete the selected
                            colleges?</span
                        >
                    </div>
                    <template #footer>
                        <Button
                            label="No"
                            icon="pi pi-times"
                            class="p-button-text"
                            @click="deleteCollegesDialog = false"
                        />
                        <Button
                            label="Yes"
                            icon="pi pi-check"
                            class="p-button-text"
                            @click="deleteSelectedColleges"
                        />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>

<script>
import CollegeList from "./CollegeList.vue";
import EditCollege from "../edit/EditCollege.vue";
import CreateCollege from "../create/CreateCollege.vue";
import { useToast } from "primevue/usetoast";

export default {
    components: { CollegeList, EditCollege, CreateCollege },
    data() {
        return {
            currentColleges: [],
            deleteCollegesDialog: false,
            selectedColleges: null,
            loading: false,
            isEmpty: false,
            errors: null,
        };
    }, //end of data

    methods: {
        createNewCollege() {
            this.college = {};
            this.$refs.createCollegeComponent.openDialog();
        }, //end of openNew

        deleteSelectedColleges() {
            this.loading = true;
            axios
                .delete("/api/admin/colleges/delete/all", {
                    data: {
                        colleges: this.selectedColleges.map((val) => val.id),
                    },
                })
                .then((response) => {
                    this.currentColleges = this.currentColleges.filter(
                        (val) => !this.selectedColleges.includes(val)
                    );
                    this.selectedColleges = null;
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
                    this.deleteCollegesDialog = false;
                });
        }, //end of deleteSelectedColleges

        confirmDeleteSelected() {
            this.deleteCollegesDialog = true;
        }, //end of confirmDeleteSelected

        exportCSV() {
            this.$refs.listCollegeComponent.exportCSV();
        }, //end of exportCSV

        fill() {
            this.loading = true;
            axios
                .get("/api/admin/colleges")
                .then((response) => {
                    this.currentColleges = response.data.colleges;
                })
                .catch((errors) => {
                    this.error = errors.response.data;
                })
                .then(() => {
                    this.loading = false;
                }); //end of axios request
        }, //end of fill function

        selectColleges(selectedColleges) {
            this.selectedColleges = selectedColleges;
        }, //end of selectColleges

        editCollege(college) {
            this.$refs.editCollegeComponent.openDialog(college);
        }, //end of editCollege
    }, //end of methods

    beforeMount() {
        this.toast = useToast();
    }, //end of beforeMount

    created() {
        this.fill();
    }, //end of created
};
</script>
