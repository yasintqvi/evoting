@extends('app.layouts.app')

@section('head-tag')
    <style>
        /* برای ثابت نگه‌داشتن ردیف در بالای صفحه */
        table {
            width: 100%;
            border-collapse: collapse;
            /* جلوگیری از ایجاد فاصله بین سلول‌ها */
        }

        thead th {
            position: sticky;
            top: 0;
            background-color: white;
            /* یا هر رنگ دلخواه برای پس‌زمینه */
            z-index: 10;
            /* بالاتر از دیگر محتواهای جدول */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            /* سایه برای جداسازی بهتر */
        }

        /* بهبود ظاهر در صورت وجود اسکرول افقی */
        table tbody {
            display: block;
            max-height: 300px;
            /* ارتفاع دلخواه */
            overflow-y: auto;
            /* اسکرول عمودی */
        }

        table thead,
        table tbody tr {
            display: table;
            width: 100%;
            table-layout: fixed;
            /* برای اینکه سلول‌ها به‌درستی نمایش داده شوند */
        }
    </style>
@endsection

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">نامزد های {{ $election->title }}</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">خانه</a></li>
                <li class="breadcrumb-item"><a href="javascript: void(0);">انتخابات</a></li>
                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $election->title }}</a></li>
                <li class="breadcrumb-item active">تعیین شرکت کنندگان</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header p-0">
                    <ul class="nav nav-tabs nav-bordered" role="tablist">
                        <li class="nav-item px-3" role="presentation">
                            <a href="#description" data-bs-toggle="tab" aria-expanded="false" class="nav-link py-2 active"
                                aria-selected="true" role="tab">
                                <span class="d-block d-sm-none"><iconify-icon icon="solar:notebook-bold"
                                        class="fs-20"></iconify-icon></span>
                                <span class="d-none d-sm-block"><iconify-icon icon="solar:notebook-bold"
                                        class="fs-14 me-1 align-middle"></iconify-icon> افزودن مشارکت کننده</span>
                            </a>
                        </li>
                        @if (in_array($election->type, [App\Enums\ElectionType::PRIVATE_JOINT, App\Enums\ElectionType::PRIVATE_JOINT_WITH_88]))
                            <li class="nav-item px-3" role="presentation">
                                <a href="#review" data-bs-toggle="tab" aria-expanded="true" class="nav-link py-2"
                                    aria-selected="false" role="tab" tabindex="-1">
                                    <span class="d-block d-sm-none"><iconify-icon icon="solar:chat-dots-bold"
                                            class="fs-20"></iconify-icon></span>
                                    <span class="d-none d-sm-block"><iconify-icon icon="solar:chat-dots-bold"
                                            class="fs-14 me-1 align-middle"></iconify-icon> افزودن مشارکت کننده به صورت
                                        جدولی</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active show" id="description" role="tabpanel">
                            <form class="col-lg-12"
                                action="{{ route('participants.store', [$group->slug, $election->id]) }}" method="post">
                                @csrf
                                <div>
                                    <div class="card-header border-bottom border-dashed">
                                        <h4 class="card-title">اطلاعات مربوط به همه پرسی</h4>
                                        <p class="text-muted mb-0">شما در حال ایجاد همه پرسی جدید هستید</p>
                                    </div>
                                    <div class="card-body">
                                        @if (session('error'))
                                            <div class="alert alert-danger">
                                                {{ session('error') }}
                                            </div>
                                        @endif
                                        @if ($errors->any())
                                            <ul class="alert alert-danger">
                                                {!! implode('', $errors->all('<div>:message</div>')) !!}
                                            </ul>
                                        @endif
                                        <div class="row" id="participants-container">
                                            <div class="col-md-3 participant-form" id="participant-form-0">
                                                <div class="mb-3">
                                                    تعداد کل سهام عادی: {{ $election->normal_stock_count }}
                                                </div>
                                            </div>
                                            <div class="col-md-3 participant-form" id="participant-form-0">
                                                <div class="mb-3">
                                                    <strong>تعداد کل سهام ممتاز: <span
                                                            id="total-prefered-stock">{{ $election->prefered_stock_count }}</span></strong>
                                                </div>
                                            </div>
                                            <div class="col-md-3 participant-form" id="participant-form-0">
                                                <div class="mb-3">
                                                    تعداد کل سهم ها:
                                                    {{ $election->prefered_stock_count * $election->prefered_stock_weight + $election->normal_stock_count }}
                                                </div>
                                            </div>
                                            <div class="col-md-3 participant-form" id="participant-form-0">
                                                <div class="mb-3">
                                                    حداقل تعداد مشارکت کنندگان: {{ $election->min_participants }}
                                                </div>
                                            </div>
                                            <div class="col-md-4 participant-form" id="participant-form-0">
                                                <div class="mb-3">
                                                    <label for="participants.0.user_id" class="form-label">مشارکت
                                                        کننده</label>
                                                    <select class="form-select my-1 my-md-0 me-sm-3"
                                                        name="participants[0][user_id]" id="participants.0.user_id"
                                                        data-toggle="select2">
                                                        <option value="">یک کاربر را انتخاب نمایید</option>
                                                        @foreach ($group->users as $user)
                                                            <option value="{{ $user->id }}"
                                                                @selected(old('participants.0.user_id') == $user->id)>
                                                                {{ $user->fullName }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('participants.0.user_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            @if (in_array($election->type, [App\Enums\ElectionType::PRIVATE_JOINT, App\Enums\ElectionType::PRIVATE_JOINT_WITH_88]))
                                                <div class="col-md-4 participant-form" id="participant-form-0">
                                                    <div class="mb-3">
                                                        <label for="participants.0.normal_stock_count"
                                                            class="form-label">تعداد سهام عادی</label>
                                                        <input type="number" class="form-control"
                                                            name="participants[0][normal_stock_count]"
                                                            id="participants.0.normal_stock_count"
                                                            placeholder="تعداد سهام عادی" required
                                                            value="{{ old('participants.0.normal_stock_count') }}">
                                                        @error('participants.0.normal_stock_count')
                                                            <span
                                                                class="text-danger font-weight-bold">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4 participant-form" id="participant-form-0">
                                                    <div class="mb-3">
                                                        <label for="participants.0.prefered_stock_count"
                                                            class="form-label">تعداد سهام ممتاز</label>
                                                        <input type="number" class="form-control"
                                                            name="participants[0][prefered_stock_count]"
                                                            id="participants.0.prefered_stock_count"
                                                            placeholder="تعداد سهام ممتاز" required
                                                            value="{{ old('participants.0.prefered_stock_count') }}">
                                                        @error('participants.0.prefered_stock_count')
                                                            <span
                                                                class="text-danger font-weight-bold">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="text-end mb-3 d-flex justify-content-between align-items-center">
                                            <button type="submit" class="btn btn-primary">ایجاد</button>
                                            <button type="button" id="add-participant-btn"
                                                class="btn btn-success">افزودن سهامدار</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @if (in_array($election->type, [App\Enums\ElectionType::PRIVATE_JOINT, App\Enums\ElectionType::PRIVATE_JOINT_WITH_88]))
                            <div class="tab-pane" id="review" role="tabpanel">
                                <div class="row ">
                                    <form
                                        action="{{ route('participants.store-table-participen', [$group->slug, $election->id]) }}"
                                        method="post" class="col-lg-12">
                                        @csrf
                                        <div>
                                            @if (session('error'))
                                                <div class="alert alert-danger">
                                                    {{ session('error') }}
                                                </div>
                                            @endif
                                            @if ($errors->any())
                                                <ul class="alert alert-danger">
                                                    {!! implode('', $errors->all('<div>:message</div>')) !!}
                                                </ul>
                                            @endif
                                            <div class="card-header border-bottom border-dashed">
                                                <h4 class="card-title">اطلاعات مربوط به همه پرسی</h4>
                                                <p class="text-muted mb-0">شما در حال ایجاد همه پرسی جدید با روش جدولی
                                                    هستید
                                                </p>
                                            </div>
                                            <div class="table-responsive-sm">
                                                <table class="table table-bordered mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>مشارکت کننده</th>
                                                            <th>تعداد سهام عادی : <span
                                                                    id="remaining-normal-stock">0</span>
                                                                <span style="display: none"
                                                                    id="initial-normal-stock">{{ $election->normal_stock_count }}</span>
                                                            </th>
                                                            <th> تعداد سهام ممتاز: <span
                                                                    id="remaining-prefered-stock">0</span>
                                                                <span style="display: none"
                                                                    id="initial-prefered-stock">{{ $election->prefered_stock_count }}</span>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($group->users as $user)
                                                            <tr>

                                                                <td>
                                                                    {{ $loop->iteration }} -

                                                                    {{ $user->fullName }}
                                                                    <input type="hidden"
                                                                        name="participants[{{ $user->id }}][user_id]"
                                                                        value="{{ $user->id }}">
                                                                </td>
                                                                <td>
                                                                    <input
                                                                        name="participants[{{ $user->id }}][normal_stock_count]"
                                                                        class="form-control normal-stock" dir="rtl"
                                                                        placeholder="تعداد سهام عادی را وارد کنید"
                                                                        style="border: none" type="number"
                                                                        min="0"
                                                                        value="{{ old('participants.' . $user->id . '.normal_stock_count') }}">
                                                                </td>
                                                                <td>
                                                                    <input
                                                                        name="participants[{{ $user->id }}][prefered_stock_count]"
                                                                        class="form-control prefered-stock" dir="rtl"
                                                                        placeholder="تعداد سهام ممتاز را وارد کنید"
                                                                        style="border: none" type="number"
                                                                        min="0"
                                                                        value="{{ old('participants.' . $user->id . '.prefered_stock_count') }}">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>


                                            </div>
                                            <div class="card-footer">
                                                <div
                                                    class="text-end mb-3 d-flex justify-content-between align-items-center">
                                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                        data-bs-target="#centermodal">ایجاد</button>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="centermodal" tabindex="-1" role="dialog"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header text-bg-info">
                                                        <h4 class="modal-title  " id="bottomModalLabel">ایحاد مشارکت کننده
                                                        </h4>
                                                        <button type="button" class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h5 class="mt-0"> شما در حال ثبت
                                                            {{ $election->normal_stock_count + $election->prefered_stock_count }}
                                                            مشارکت‌کننده هستید!!</h5>
                                                        <p class="mb-0">آیا از انجام این عملیات اطمینان دارید؟"</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light"
                                                            data-bs-dismiss="modal">بستن</button>
                                                        <button type="submit" class="btn btn-info">ادامه دادن</button>
                                                    </div>
                                                </div><!-- /.modal-content -->

                                            </div><!-- /.modal-dialog -->
                                        </div><!-- /.modal -->
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (in_array($election->type, [App\Enums\ElectionType::PRIVATE_JOINT, App\Enums\ElectionType::PRIVATE_JOINT_WITH_88]))
        <form class="card col-lg-5" action="{{ route('participants.import', [$group->slug, $election->id]) }}"
            method="POST" enctype="multipart/form-data">
            @csrf
            <div>
                <div class="card-header col-lg-12 border-bottom border-dashed d-flex align-items-center">
                    <div class="col-lg-6">
                        <h4 class="header-title">آپلود فایل اکسل کاربران</h4>
                    </div>
                    <div class="col-lg-6 text-end">
                        <a href="{{ asset('assets/excel/نمونه اکسل شرکت کنندگان.xlsx') }}" download=""
                            class="header-title" style="cursor: pointer">فایل نمونه اکسل</a>
                    </div>
                </div>

                <div class="card-body">

                    <div class="mb-3">
                        <label for="formFileMultiple" class="form-label">قایل مورد نظر را انتخاب کنید</label>
                        <input class="form-control" name="file" type="file" id="formFileMultiple">
                    </div>

                    <!-- Preview -->
                </div>
                <div class="card-footer">
                    <div class="text-end mb-3">
                        <button type="submit" class="btn btn-primary"> ایجاد مشارکت کننده با اکسل</button>
                    </div>
                </div>
                <!-- end card-body -->
            </div>
        </form>
    @endif
@endsection

@section('scripts')
    {{-- script to stop tab --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.localStorage.getItem('activeTab')) {
                const activeTab = window.localStorage.getItem('activeTab');
                const tabElement = document.querySelector(`a[href="#${activeTab}"]`);
                if (tabElement) {
                    document.querySelectorAll('.tab-pane').forEach(function(tabContent) {
                        tabContent.classList.remove('active');
                    });
                    document.querySelectorAll('.nav-link').forEach(function(tab) {
                        tab.classList.remove('active');
                    });


                    tabElement.classList.add('active');
                    const tabContent = document.querySelector(tabElement.getAttribute('href'));
                    tabContent.classList.add('active');
                }
            }

            const tabs = document.querySelectorAll('.nav-link');
            tabs.forEach(function(tab) {
                tab.addEventListener('click', function() {
                    const targetTab = tab.getAttribute('href').substring(1);
                    window.localStorage.setItem('activeTab', targetTab);
                });
            });
        });
    </script>
    {{-- script for counter to stock --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function calculateRemainingStock() {
                let initialPreferedStock = parseInt(document.getElementById("initial-prefered-stock")
                    .textContent) || 0;

                let initialNormaldStock = parseInt(document.getElementById("initial-normal-stock")
                    .textContent) || 0;

                let totalNormalStockEntered = 0;
                let totalPreferedStockEntered = 0;

                document.querySelectorAll(".prefered-stock").forEach(input => {
                    totalPreferedStockEntered += parseInt(input.value) || 0;
                });

                document.querySelectorAll(".normal-stock").forEach(input => {
                    totalNormalStockEntered += parseInt(input.value) || 0;
                });

                let remainingStock = initialPreferedStock - totalPreferedStockEntered;
                let remainingNormalStock = initialNormaldStock - totalNormalStockEntered;

                console.log("Remaining Prefered Stock:", remainingStock);
                console.log("Remaining Normal Stock:", remainingNormalStock);

                document.getElementById("remaining-prefered-stock").textContent = remainingStock;
                document.getElementById("remaining-normal-stock").textContent = remainingNormalStock;
            }

            document.querySelectorAll(".prefered-stock ,  .normal-stock").forEach(input => {
                input.addEventListener("input", calculateRemainingStock);
            });

            calculateRemainingStock();
        });
    </script>

    <script>
        let participantIndex = 1;

        document.getElementById('add-participant-btn').addEventListener('click', function() {
            const participantsContainer = document.getElementById('participants-container');

            const newParticipantForm = `
        @if (in_array($election->type, [App\Enums\ElectionType::PUBLIC_JOINT]))
         <div class="col-md-4 participant-form mt-1" data-id="${participantIndex}">
            <label for="participants.${participantIndex}.user_id" class="form-label mb-0">مشارکت کننده</label>
            <section class="d-flex align-items-center gap-2">
            <select class="form-select flex-grow-1" name="participants[${participantIndex}][user_id]" id="participants.${participantIndex}.user_id">
                <option value="">یک کاربر را انتخاب نمایید</option>
                @foreach ($group->users as $user)
                    <option value="{{ $user->id }}" @selected(old('participants.${participantIndex}.user_id') == $user->id) >{{ $user->fullName }}</option>
                @endforeach
            </select>
            <button type="button" class="btn btn-outline-danger remove-participant-btn" data-id="${participantIndex}">−</button>
            </section>
            @error('participants.${participantIndex}.user_id')
            <span class="text-danger w-100">{{ $message }}</span>
            @enderror
        </div>

        @endif
        @if (in_array($election->type, [App\Enums\ElectionType::PRIVATE_JOINT, App\Enums\ElectionType::PRIVATE_JOINT_WITH_88]))
    <div class="row participant-form" data-id="${participantIndex}">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="participants.${participantIndex}.user_id" class="form-label">مشارکت کننده</label>
                <select class="form-select" name="participants[${participantIndex}][user_id]" id="participants.${participantIndex}.user_id">
                    <option value="">یک کاربر را انتخاب نمایید</option>
                    @foreach ($group->users as $user)
                        <option value="{{ $user->id }}" @selected(old('participants.${participantIndex}.user_id') == $user->id) >{{ $user->fullName }}</option>
                    @endforeach
                </select>
                @error('participants.${participantIndex}.user_id')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="participants.${participantIndex}.normal_stock_count" class="form-label">تعداد سهام عادی:</label>
                <input type="number" class="form-control" name="participants[${participantIndex}][normal_stock_count]" id="participants.${participantIndex}.normal_stock_count" placeholder="تعداد سهام عادی" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="participants.${participantIndex}.prefered_stock_count" class="form-label">تعداد سهام ممتاز</label>
                <div class="d-flex align-items-center gap-2">
                    <input type="number" class="form-control" name="participants[${participantIndex}][prefered_stock_count]" id="participants.${participantIndex}.prefered_stock_count" placeholder="تعداد سهام ممتاز" required>
                    <button type="button" class="btn btn-outline-danger remove-participant-btn" data-id="${participantIndex}">−</button>
                </div>
            </div>
        </div>
        @endif
    </div>
`;
            document.getElementById('participants-container').insertAdjacentHTML('beforeend', newParticipantForm);
            participantIndex++;

        });

        document.getElementById('participants-container').addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-participant-btn')) {
                event.target.closest('.participant-form').remove();
            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const totalStockElement = document.getElementById("total-prefered-stock");
            const stockInput = document.getElementById("participants.0.prefered_stock_count");

            const initialTotalStock = parseInt(totalStockElement.textContent, 10) || 0;

            stockInput.addEventListener("input", function() {
                let allocatedStock = parseInt(stockInput.value, 10) || 0;
                let remainingStock = initialTotalStock - allocatedStock;
                totalStockElement.textContent = remainingStock >= 0 ? remainingStock : 0;
            });
        });
    </script>





    @include('app.alerts.toastr.success')
    <script src="{{ asset('assets/vendor/gridjs/gridjs.umd.js') }}"></script>
    <script src="{{ asset('assets/js/pages/table-gridjs.js') }}"></script>
@endsection
