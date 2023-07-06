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
                                @click="createNewEvent"
                            />
                            <Button
                                :label="$t('delete')"
                                icon="pi pi-trash"
                                class="p-button-danger"
                                @click="confirmDeleteSelected"
                                :disabled="
                                    !selectedEvents || !selectedEvents.length
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

                <event-list
                    ref="listEventComponent"
                    :currentEvents="currentEvents"
                    @selectEvents="selectEvents"
                    @editEvent="editEvent"
                    @deleteEvent="fill"
                ></event-list>

                <edit-event ref="editEventComponent" ></edit-event>

                <create-event
                    ref="createEventComponent"
                    @eventCreated="fill"
                ></create-event>

                <Dialog
                    v-model:visible="deleteEventsDialog"
                    :style="{ width: '450px' }"
                    header="Confirm"
                    :modal="true"
                >
                    <div class="flex align-items-center justify-content-center">
                        <i
                            class="pi pi-exclamation-triangle mr-3"
                            style="font-size: 2rem"
                        />
                        <span v-if="event"
                            >Are you sure you want to delete the selected
                            events?</span
                        >
                    </div>
                    <template #footer>
                        <Button
                            label="No"
                            icon="pi pi-times"
                            class="p-button-text"
                            @click="deleteEventsDialog = false"
                        />
                        <Button
                            label="Yes"
                            icon="pi pi-check"
                            class="p-button-text"
                            @click="deleteSelectedEvents"
                        />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>

<script>
import EventList from "./EventList.vue";
import EditEvent from "../edit/EditEvent.vue";
import CreateEvent from "../create/CreateEvent.vue";
import { useToast } from "primevue/usetoast";

export default {
    components: { EventList, EditEvent, CreateEvent },
    data() {
        return {
            currentEvents: [],
            deleteEventsDialog: false,
            selectedEvents: null,
            loading: false,
            isEmpty: false,
            errors: null,
        };
    }, //end of data

    methods: {
        createNewEvent() {
            this.event = {};
            this.$refs.createEventComponent.openDialog();
        }, //end of openNew

        deleteSelectedEvents() {
            this.loading = true;
            axios
                .delete("/api/admin/events/delete/all", {
                    data: {
                        events: this.selectedEvents.map((val) => val.id),
                    },
                })
                .then((response) => {
                    this.currentEvents = this.currentEvents.filter(
                        (val) => !this.selectedEvents.includes(val)
                    );
                    this.selectedEvents = null;
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
                    this.deleteEventsDialog = false;
                });
        }, //end of deleteSelectedEvents

        confirmDeleteSelected() {
            this.deleteEventsDialog = true;
        }, //end of confirmDeleteSelected

        exportCSV() {
            this.$refs.listEventComponent.exportCSV();
        }, //end of exportCSV

        fill() {
            this.loading = true;
            axios
                .get("/api/admin/events")
                .then((response) => {
                    this.currentEvents = response.data.events;
                    console.log(this.currentEvents);
                    // console.log(this.currentEvents);
                    // console.log(this.departments);
                })
                .catch((errors) => {
                    this.error = errors.response.data;
                })
                .then(() => {
                    this.loading = false;
                }); //end of axios request
        }, //end of fill function

        selectEvents(selectedEvents) {
            this.selectedEvents = selectedEvents;
        }, //end of selectEvents

        editEvent(event) {
            this.$refs.editEventComponent.openDialog(event);
        }, //end of editEvent
    }, //end of methods

    beforeMount() {
        this.toast = useToast();
    }, //end of beforeMount

    created() {
        this.fill();
    }, //end of created
};
</script>
