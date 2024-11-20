<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        User KPI
    </x-slot>
    <div class="flex flex-col gap-4">
        <div
            class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    {!! view_render_event('admin.settings.roles.index.breadcrumbs.before') !!}

                    <!-- Breadcumbs -->
                    {{-- <x-admin::breadcrumbs name="user.user_kpis" /> --}}

                    {!! view_render_event('admin.settings.roles.index.breadcrumbs.after') !!}
                </div>

                <div class="text-xl font-bold dark:text-white">
                    <!-- title -->
                    @lang('admin::app.settings.roles.index.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                <!-- Create button Roles -->
                <div class="flex items-center gap-x-2.5">
                    {!! view_render_event('admin.settings.roles.index.create_button.before') !!}

                    @if (bouncer()->hasPermission('settings.user.roles.create'))
                        <button type="button" class="primary-button" @click="$refs.composeMail.toggleModal()">
                            Import
                        </button>
                    @endif
                </div>
            </div>


        </div>
        <v-mail ref="composeMail">
            <!-- Datagrid Shimmer -->
            <x-admin::shimmer.mail.datagrid />
        </v-mail>

        <x-admin::datagrid :src="route('admin.user_kpi.index')">
            <!-- DataGrid Shimmer -->
            <x-admin::shimmer.datagrid />
        </x-admin::datagrid>
    </div>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-mail-template"
        >
            {!! view_render_event('admin.mail.'.request('route').'.datagrid.before') !!}

           <!-- DataGrid -->
           <x-admin::datagrid
                ref="datagrid"
                :src="route('admin.mail.index', request('route'))"
            >
                <template #header="{
                    isLoading,
                    available,
                    applied,
                    selectAll,
                    sort,
                    performAction
                }">
                    <div></div>
                </template>

                <template #body="{
                    isLoading,
                    available,
                    applied,
                    selectAll,
                    sort,
                    performAction
                }">

                    <template v-if="isLoading">
                        <x-admin::shimmer.mail.datagrid.table.body />
                    </template>

                    <template v-else>
                        <div
                            v-for="record in available.records"
                            class="flex cursor-pointer items-center justify-between border-b px-8 py-4 text-gray-600 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-950"
                            :class="{
                                'font-medium': record.is_read,
                                'font-semibold': ! record.is_read
                            }"
                            @click.stop="selectedMail=true; editModal(record.actions.find(action => action.index === 'edit'))"
                        >
                            <!-- Select Box -->
                            <div class="flex w-full items-center justify-start gap-32">
                                <div class="flex items-center gap-6">
                                    <div class="relative flex items-center">
                                        <!-- Dot Indicator -->
                                        <span
                                            class="absolute right-8 h-1.5 w-1.5 rounded-full bg-sky-600 dark:bg-white"
                                            v-if="! record.is_read"
                                        ></span>

                                        <!-- Checkbox Container -->
                                        <div class="flex items-center gap-2">
                                            <input
                                                type="checkbox"
                                                :name="`mass_action_select_record_${record.id}`"
                                                :id="`mass_action_select_record_${record.id}`"
                                                :value="record.id"
                                                class="peer hidden"
                                                v-model="applied.massActions.indices"
                                                @click.stop
                                            >

                                            <label
                                                class="icon-checkbox-outline peer-checked:icon-checkbox-select cursor-pointer rounded-md text-2xl !text-gray-500 peer-checked:!text-brandColor dark:!text-gray-300"
                                                :for="`mass_action_select_record_${record.id}`"
                                                @click.stop
                                            ></label>
                                        </div>
                                    </div>

                                    <p class="overflow-hidden text-ellipsis whitespace-nowrap leading-none">@{{ record.name }}</p>
                                </div>

                                <div class="flex w-full items-center justify-between gap-4">
                                    <!-- Content -->
                                    <div class="flex-frow flex items-center gap-2">
                                        <!-- Attachments -->
                                        <p v-html="record.attachments"></p>

                                        <!-- Tags -->
                                        <span
                                            class="flex items-center gap-1 rounded-md bg-rose-100 px-3 py-1.5 text-xs font-medium"
                                            :style="{
                                                'background-color': tag.color,
                                                'color': backgroundColors.find(color => color.background === tag.color)?.text
                                            }"
                                            v-for="(tag, index) in record.tags"
                                            v-html="tag.name"
                                        >
                                        </span>

                                        <!-- Subject -->
                                        <p v-text="record.subject"></p>

                                        <!-- Reply(Content) -->
                                        <p
                                            class="!font-normal"
                                            v-html="truncatedReply(record.reply)"
                                        ></p>
                                    </div>

                                    <!-- Time -->
                                    <div class="min-w-[80px] flex-shrink-0 text-right">
                                        <p class="leading-none">@{{ record.created_at }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </template>
            </x-admin::datagrid>

            {!! view_render_event('admin.mail.'.request('route').'.datagrid.after') !!}

            {!! view_render_event('admin.mail.create.form.before') !!}

            <x-admin::form v-slot="{ meta, errors, handleSubmit }" enctype="multipart/form-data" as="div">
                <form @submit="handleSubmit($event, save)" ref="mailForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <x-admin::modal ref="toggleComposeModal" position="bottom-right">
                        <x-slot:header>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Chọn file</h3>
                        </x-slot>

                        <x-slot:content>
                            <!-- Attachments -->
                            <x-admin::form.control-group>
                                <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ trans('admin::app.mail.index.mail.attachments') }}
                                </label>
                                <input
                                    type="file"
                                    id="file"
                                    name="file"
                                    class="mt-1 block w-full"
                                    accept=".xlsx,.xls"
                                    required
                                />
                            </x-admin::form.control-group>
                        </x-slot>

                        <x-slot:footer>
                            <div class="flex w-full items-center justify-between">
                                <label
                                    class="icon-attachment cursor-pointer rounded-md p-1 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800"
                                    for="file"
                                ></label>

                                <div class="flex items-center gap-4">
                                    <x-admin::button
                                        class="primary-button"
                                        type="submit"
                                        ref="submitBtn"
                                        title="Import"
                                    />
                                </div>
                            </div>
                        </x-slot>
                    </x-admin::modal>
                </form>
            </x-admin::form>


            {!! view_render_event('admin.mail.create.form.after') !!}
        </script>

        <script type="module">
            app.component('v-mail', {
                template: '#v-mail-template',

                data() {
                    return {
                        isStoring: false,

                        backgroundColors: [{
                            label: "@lang('admin::app.components.tags.index.aquarelle-red')",
                            text: '#DC2626',
                            background: '#FEE2E2',
                        }, {
                            label: "@lang('admin::app.components.tags.index.crushed-cashew')",
                            text: '#EA580C',
                            background: '#FFEDD5',
                        }, {
                            label: "@lang('admin::app.components.tags.index.beeswax')",
                            text: '#D97706',
                            background: '#FEF3C7',
                        }, {
                            label: "@lang('admin::app.components.tags.index.lemon-chiffon')",
                            text: '#CA8A04',
                            background: '#FEF9C3',
                        }, {
                            label: "@lang('admin::app.components.tags.index.snow-flurry')",
                            text: '#65A30D',
                            background: '#ECFCCB',
                        }, {
                            label: "@lang('admin::app.components.tags.index.honeydew')",
                            text: '#16A34A',
                            background: '#DCFCE7',
                        }, ],
                    };
                },

                methods: {
                    toggleModal() {
                        this.$refs.toggleComposeModal.toggle();
                    },

                    save(params, {
                        resetForm,
                        setErrors
                    }) {
                        this.isStoring = true;

                        let formData = new FormData(this.$refs.mailForm);

                        this.$axios.post("{{ route('admin.user_kpi.import') }}", formData, {
                                headers: {
                                    'Content-Type': 'multipart/form-data',
                                },
                            })
                            .then((response) => {
                                this.$refs.datagrid.get();

                                this.$emitter.emit('add-flash', {
                                    type: 'success',
                                    message: response.data?.message
                                });

                                resetForm();
                            })
                            .catch((error) => {
                                if (error?.response?.status == 422) {
                                    setErrors(error.response.data.errors);
                                } else {
                                    this.$emitter.emit('add-flash', {
                                        type: 'error',
                                        message: error.response.data.message
                                    });
                                }
                            }).finally(() => {
                                this.$refs.toggleComposeModal.close();

                                this.isStoring = false;

                                this.resetForm();
                            });
                    },

                    editModal(row) {
                        if (row.title == 'View') {
                            window.location.href = row.url;

                            return;
                        }

                        this.$axios.get(row.url)
                            .then(response => {
                                this.draft = response.data.data;

                                this.$refs.toggleComposeModal.toggle();

                                this.showCC = this.draft.cc.length > 0;

                                this.showBCC = this.draft.bcc.length > 0;

                            })
                            .catch(error => {});
                    },

                    resetForm() {
                        this.draft = {
                            id: null,
                            attachments: [],
                        };
                    },
                },
            });
        </script>
    @endPushOnce

</x-admin::layouts>
