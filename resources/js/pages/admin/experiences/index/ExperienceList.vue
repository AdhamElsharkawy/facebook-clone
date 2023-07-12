<template>
    <Loading v-if="loading" />
    <DataTable
        ref="dt"
        :value="experiences"
        v-model:selection="selectedExperiences"
        dataKey="id"
        :paginator="true"
        :rows="10"
        :filters="filters"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[5, 10, 25]"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} experiences"
        responsiveLayout="scroll"
    >
        <template #header>
            <div
                class="flex flex-column md:flex-row md:justify-content-between md:align-items-center"
                :class="{ 'md:flex-row-reverse': $store.getters['isRtl'] }"
            >
                <h5 class="m-0">{{ $t("manage") + " " + "experiences" }}</h5>
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
            field="title"
            header="title"
            :sortable="true"
            headerStyle="width:14%; min-width:10rem;"
            :class="{ 'text-right': $store.getters['isRtl'] }"
        >
            <template #body="slotProps">
                <span class="p-column-title">Degree</span>
              {{ slotProps.data.title }}
            </template>
        </Column>
        <Column
            field="description"
            header="description"
            :sortable="true"
            headerStyle="width:16%; min-width:10rem;"
            :class="{ 'text-right': $store.getters['isRtl'] }"
        >
            <template #body="slotProps">
                <span class="p-column-title">description</span>
                <div class="description">{{ slotProps.data.description }}</div>
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
            field="type"
            header="type"
            :sortable="true"
            headerStyle="width:14%; min-width:14rem;"
            :class="{ 'text-right': $store.getters['isRtl'] }"
        >
            <template #body="slotProps">
                <span class="p-column-title">Type</span>
                {{ slotProps.data.type}}
            </template>
        </Column>

        <Column
            field="company"
            header="company"
            :sortable="true"
            headerStyle="width:14%; min-width:14rem;"
            :class="{ 'text-right': $store.getters['isRtl'] }"
        >
            <template #body="slotProps">
                <span class="p-column-title">company</span>
                {{ slotProps.data.company?.name }}
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
                    @click="editExperience(slotProps.data)"
                />
                <Button
                    icon="pi pi-trash"
                    class="p-button-rounded p-button-warning mx-2"
                    @click="confirmDeleteExperience(slotProps.data)"
                />
            </template>
        </Column>
    </DataTable>
    <Dialog
        v-model:visible="deleteExperienceDialog"
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
                >Are you sure you want to delete <b>{{ experience.name }}</b
                >?</span
            >
        </div>
        <template #footer>
            <Button
                label="No"
                icon="pi pi-times"
                class="p-button-text"
                @click="deleteExperienceDialog = false"
            />
            <Button
                label="Yes"
                icon="pi pi-check"
                class="p-button-text"
                @click="deleteExperience"
            />
        </template>
    </Dialog>
</template>

<script>
import { FilterMatchMode } from "primevue/api";
import { useToast } from "primevue/usetoast";

export default {
    props: {
        currentExperiences: {
            type: Array,
            required: true,
        },

    }, //end of props

    emits: ["selectExperiences", "deleteExperience", "editExperience"],

    data() {
        return {
            toast: null,
            loading: false,
            experienceDialog: false,
            deleteExperienceDialog: false,
            experience: {},
            experiences: this.currentExperiences,
            selectedExperiences: null,
            filters: {
                global: { value: null, matchMode: FilterMatchMode.CONTAINS },
            },
        };
    }, //end of data

    watch: {
        selectedExperiences(val) {
            this.$emit("selectExperiences", val);
        },
    }, //end of watch

    beforeMount() {
        this.initFilters();
        this.toast = useToast();
        console.log(this.experiences);
    }, //end of beforeMount

    methods: {
        confirmDeleteExperience(experience) {
            this.experience = experience;
            this.deleteExperienceDialog = true;
        }, //end of confirmDeleteExperience

        deleteExperience() {
            this.loading = true;
            axios
                .delete("/api/admin/experiences/" + this.experience.id)
                .then((response) => {
                    this.toast.add({
                        severity: "success",
                        summary: "Successful",
                        detail: response.data.message,
                        life: 3000,
                    });
                    this.$emit("deleteExperience");
                    this.deleteExperienceDialog = false;
                    this.experience = {};
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
                    this.deleteExperienceDialog = false;
                });
        }, //end of deleteExperience

        initFilters() {
            this.filters = {
                global: { value: null, matchMode: FilterMatchMode.CONTAINS },
            };
        }, //end of initFilters

        exportCSV() {
            this.$refs.dt.exportCSV();
        }, //end of exportCSV

        editExperience(experience) {
            this.$emit("editExperience", experience);
        }, //end of editExperience
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
