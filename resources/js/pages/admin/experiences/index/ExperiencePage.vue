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
                                @click="createNewExperience"
                            />
                            <Button
                                :label="$t('delete')"
                                icon="pi pi-trash"
                                class="p-button-danger"
                                @click="confirmDeleteSelected"
                                :disabled="
                                    !selectedExperiences || !selectedExperiences.length
                                "
                            />
                        </div>
                    </template>

                    <template v-slot:end>
                        <Button
                            :label="$t('export')"
                            icon="pi pi-upload"
                            class="p-button-help"
                            @click="exportCSV($experience)"
                        />
                    </template>
                </Toolbar>

                <experience-list
                    ref="listExperienceComponent"
                    :currentExperiences="currentExperiences"
                    :totalPages="totalPages"
                    :currentPage="currentPage"
                    @pageChange="pageChange"
                    @selectExperiences="selectExperiences"
                    @editExperience="editExperience"
                    @deleteExperience="fill"
                ></experience-list>

                <edit-experience ref="editExperienceComponent" :companies="companies" :users="users" ></edit-experience>

                <create-experience
                    ref="createExperienceComponent"
                    @experienceCreated="fill"
                    :companies="companies"
                    :users="users"
                ></create-experience>

                <Dialog
                    v-model:visible="deleteExperiencesDialog"
                    :style="{ width: '450px' }"
                    header="Confirm"
                    :modal="true"
                >
                    <div class="flex align-items-center justify-content-center">
                        <i
                            class="pi pi-exclamation-triangle mr-3"
                            style="font-size: 2rem"
                        />
                        <span v-if="experience"
                            >Are you sure you want to delete the selected
                            experiences?</span
                        >
                    </div>
                    <template #footer>
                        <Button
                            label="No"
                            icon="pi pi-times"
                            class="p-button-text"
                            @click="deleteExperiencesDialog = false"
                        />
                        <Button
                            label="Yes"
                            icon="pi pi-check"
                            class="p-button-text"
                            @click="deleteSelectedExperiences"
                        />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>

<script>
import ExperienceList from "./ExperienceList.vue";
import EditExperience from "../edit/EditExperience.vue";
import CreateExperience from "../create/CreateExperience.vue";
import { useToast } from "primevue/usetoast";

export default {
    components: { ExperienceList, EditExperience, CreateExperience },
    data() {
        return {
            currentExperiences: [],
            deleteExperiencesDialog: false,
            selectedExperiences: null,
            loading: false,
            isEmpty: false,
            errors: null,
            companies: [],
            users: [],
        };
    }, //end of data

    methods: {
        createNewExperience() {
            this.experience = {};
            this.$refs.createExperienceComponent.openDialog();
        }, //end of openNew

        deleteSelectedExperiences() {
            this.loading = true;
            axios
                .delete("/api/admin/experiences/delete/all", {
                    data: {
                        experiences: this.selectedExperiences.map((val) => val.id),
                    },
                })
                .then((response) => {
                    this.currentExperiences = this.currentExperiences.filter(
                        (val) => !this.selectedExperiences.includes(val)
                    );
                    this.selectedExperiences = null;
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
                    this.deleteExperiencesDialog = false;
                });
        }, //end of deleteSelectedExperiences

        confirmDeleteSelected() {
            this.deleteExperiencesDialog = true;
        }, //end of confirmDeleteSelected

        exportCSV() {
            this.$refs.listExperienceComponent.exportCSV();
        }, //end of exportCSV

        fill(currentPage) {
            this.loading = true;
            axios
                .get(`/api/admin/experiences?page=${currentPage}`)
                .then((response) => {
                    this.currentExperiences = response.data.experiences.data;
                    this.totalPages = response.data.experiences.last_page;
                    this.currentPage = response.data.experiences.current_page;
                    this.companies = response.data.companies;
                    this.users = response.data.users;
                    console.log(response.data.experiences.data);
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

        selectExperiences(selectedExperiences) {
            this.selectedExperiences = selectedExperiences;
        }, //end of selectExperiences

        editExperience(experience) {
            this.$refs.editExperienceComponent.openDialog(experience);
        }, //end of editExperience
    }, //end of methods

    beforeMount() {
        this.toast = useToast();
    }, //end of beforeMount

    created() {
        this.fill();
    }, //end of created
};
</script>
