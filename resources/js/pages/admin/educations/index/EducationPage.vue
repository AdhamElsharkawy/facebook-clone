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
                                @click="createNewEducation"
                            />
                            <Button
                                :label="$t('delete')"
                                icon="pi pi-trash"
                                class="p-button-danger"
                                @click="confirmDeleteSelected"
                                :disabled="
                                    !selectedEducations || !selectedEducations.length
                                "
                            />
                        </div>
                    </template>

                    <template v-slot:end>
                        <Button
                            :label="$t('export')"
                            icon="pi pi-upload"
                            class="p-button-help"
                            @click="exportCSV($education)"
                        />
                    </template>
                </Toolbar>

                <education-list
                    ref="listEducationComponent"
                    :currentEducations="currentEducations"
                    :totalPages="totalPages"
                    :currentPage="currentPage"
                    @pageChange="pageChange"
                    @selectEducations="selectEducations"
                    @editEducation="editEducation"
                    @deleteEducation="fill"
                ></education-list>

                <edit-education ref="editEducationComponent" :colleges="colleges" :users="users" ></edit-education>

                <create-education
                    ref="createEducationComponent"
                    @educationCreated="fill"
                    :colleges="colleges"
                    :users="users"
                ></create-education>

                <Dialog
                    v-model:visible="deleteEducationsDialog"
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
                            >Are you sure you want to delete the selected
                            educations?</span
                        >
                    </div>
                    <template #footer>
                        <Button
                            label="No"
                            icon="pi pi-times"
                            class="p-button-text"
                            @click="deleteEducationsDialog = false"
                        />
                        <Button
                            label="Yes"
                            icon="pi pi-check"
                            class="p-button-text"
                            @click="deleteSelectedEducations"
                        />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>

<script>
import EducationList from "./EducationList.vue";
import EditEducation from "../edit/EditEducation.vue";
import CreateEducation from "../create/CreateEducation.vue";
import { useToast } from "primevue/usetoast";

export default {
    components: { EducationList, EditEducation, CreateEducation },
    data() {
        return {
            currentEducations: [],
            deleteEducationsDialog: false,
            selectedEducations: null,
            loading: false,
            isEmpty: false,
            errors: null,
            colleges: [],
            users: [],
            totalPages: 0,
            currentPage: 1,
        };
    }, //end of data

    methods: {
        createNewEducation() {
            this.education = {};
            this.$refs.createEducationComponent.openDialog();
        }, //end of openNew

        deleteSelectedEducations() {
            this.loading = true;
            axios
                .delete("/api/admin/educations/delete/all", {
                    data: {
                        educations: this.selectedEducations.map((val) => val.id),
                    },
                })
                .then((response) => {
                    this.currentEducations = this.currentEducations.filter(
                        (val) => !this.selectedEducations.includes(val)
                    );
                    this.selectedEducations = null;
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
                    this.deleteEducationsDialog = false;
                });
        }, //end of deleteSelectedEducations

        confirmDeleteSelected() {
            this.deleteEducationsDialog = true;
        }, //end of confirmDeleteSelected

        exportCSV() {
            this.$refs.listEducationComponent.exportCSV();
        }, //end of exportCSV

        fill(currentPage) {
            this.loading = true;
            axios
                .get(`/api/admin/educations?page=${currentPage}`)
                .then((response) => {
                    console.log(response.data);
                    this.currentEducations = response.data.educations.data;
                    this.colleges = response.data.colleges;
                    this.users = response.data.users;
                    this.totalPages = response.data.educations.last_page;
                    this.currentPage = response.data.educations.current_page;
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

        selectEducations(selectedEducations) {
            this.selectedEducations = selectedEducations;
        }, //end of selectEducations

        editEducation(education) {
            this.$refs.editEducationComponent.openDialog(education);
        }, //end of editEducation
    }, //end of methods

    beforeMount() {
        this.toast = useToast();
    }, //end of beforeMount

    created() {
        this.fill();
    }, //end of created
};
</script>
