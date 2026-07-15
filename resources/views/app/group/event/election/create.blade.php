@extends('app.layouts.app')

@section('content')
    @php
        $prefill = $prefill ?? null;
        $existingElectionTitles = $existingElectionTitles ?? [];
        $oldBlocked = old('blocked_user_ids', $prefill['blocked_user_ids'] ?? []);
        $oldType = old('type', $prefill['type'] ?? null);
        $oldPositionId = old('position_id', $prefill['position_id'] ?? null);
        $oldTitle = old('title', $prefill['title'] ?? null);
        $oldMain = old('main_member_count', $prefill['main_member_count'] ?? 1);
        $oldSubstitute = old('substitute_member_count', $prefill['substitute_member_count'] ?? 0);
    @endphp
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">ایجاد همه پرسی</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">خانه</a></li>

                <li class="breadcrumb-item"><a href="javascript: void(0);">انتخابات</a></li>

                <li class="breadcrumb-item active">ایجاد</li>
            </ol>
        </div>
    </div>

    <form id="electionCreateForm" action="{{ route('elections.store', [$group, $event]) }}" method="post"
        onsubmit="return validateElectionCreateSubmit(event)">
        @csrf
        <div class="card col-lg-6 mb-3">
            <div class="card-header border-bottom border-dashed">
                <h4 class="card-title">اطلاعات مربوط به همه پرسی</h4>
                <p class="text-muted mb-0">شما در حال ایجاد همه پرسی جدید هستید</p>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="title" class="form-label">عنوان همه پرسی</label>
                            <input type="text" name="title" value="{{ $oldTitle }}" class="form-control"
                                id="title">
                            @error('title')
                                <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="election_type" class="form-label">نوع همه پرسی</label>
                            <select name="type" onchange="checkElectionType(event)" id="election_type"
                                data-toggle="select2" class="form-select">
                                <option value="">یک نوع همه پرسی را انتخاب نمایید</option>
                                @if ($group->type === App\Enums\GroupType::SPECIAL)
                                    <option @selected($oldType == App\Enums\ElectionType::PRIVATE_JOINT_WITH_88->value)
                                        value="{{ App\Enums\ElectionType::PRIVATE_JOINT_WITH_88->value }}">سهامی خاص با ماده
                                        ۸۸</option>
                                    <option @selected($oldType == App\Enums\ElectionType::PRIVATE_JOINT->value)
                                        value="{{ App\Enums\ElectionType::PRIVATE_JOINT->value }}">سهامی خاص بدون ماده ۸۸
                                    </option>
                                @else
                                    <option @selected(($oldType ?: App\Enums\ElectionType::PUBLIC_JOINT->value) == App\Enums\ElectionType::PUBLIC_JOINT->value)
                                        value="{{ App\Enums\ElectionType::PUBLIC_JOINT->value }}">انتخابات تعاونی</option>
                                @endif
                            </select>
                            @error('type')
                                <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="position_id" class="form-label">مقام انتخاباتی</label>
                            <select name="position_id" id="position_id" class="form-select">
                                <option value="">یک مقام را انتخاب نمایید</option>
                                @foreach ($positions as $position)
                                    <option value="{{ $position->id }}" @selected((string) $oldPositionId === (string) $position->id)>{{ $position->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('position_id')
                                <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="main_member_count" class="form-label">تعداد عضو اصلی </label>
                            <input type="number" class="form-control" id="main_member_count" name="main_member_count"
                                value="{{ $oldMain }}" min="1" onchange="validateCandidateCounts()">
                            @error('main_member_count')
                                <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="substitute_member_count" class="form-label">تعداد عضو علی البدل </label>
                            <input type="number" class="form-control" name="substitute_member_count"
                                value="{{ $oldSubstitute }}" id="substitute_member_count" min="0"
                                onchange="validateCandidateCounts()">
                            @error('substitute_member_count')
                                <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="alert alert-info" id="candidate-info" style="display: none;">
                            <i class="ri-information-line"></i> <span id="candidate-info-text"></span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="alert alert-light border mb-0">
                            <small class="text-muted">زمان شروع و پایان انتخابات در این مرحله ثبت نمی‌شود. هنگام زدن دکمهٔ «شروع انتخابات» در لیست انتخابات، در صورت تمایل می‌توانید فقط <strong>زمان پایان</strong> (اختیاری) را وارد کنید.</small>
                        </div>
                    </div>
                    <div class="col-12">
                        <!-- حذف گزینه وزن سهام ممتاز از صفحه ایجاد انتخابات -->
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="blocked_user_ids" class="form-label">عدم اجازه شرکت در رأی‌گیری</label>
                            <select name="blocked_user_ids[]" id="blocked_user_ids" class="form-select" multiple
                                data-toggle="select2">
                                @foreach ($event->participants as $p)
                                    <option value="{{ $p->user->id }}" @selected(in_array((int) $p->user->id, array_map('intval', (array) $oldBlocked), true))>
                                        {{ $p->user->first_name }} {{ $p->user->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block">کاربرانی که انتخاب شوند، امکان رأی‌دادن در این انتخابات را
                                نخواهند داشت.</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="text-end mb-3 d-flex">
                    <button type="submit" class="btn btn-primary">ایجاد </button>
                </div>
            </div>
        </div>

        @can(\App\Enums\Permission::CREATE_ELECTIONS->value)
            <div class="card col-lg-6 border-dashed">
                <div class="card-header border-bottom border-dashed">
                    <h4 class="card-title fs-16 mb-0">چسباندن قالب از متن کپی‌شده</h4>
                    <p class="text-muted small mb-0 mt-1">فقط مشخصات همه‌پرسی در قالب زیر است؛ نام افراد داخل متن نیست.
                        پس از اعمال، عنوان به‌صورت خودکار طوری تنظیم می‌شود که با عنوانهای موجود در این رویداد یکسان
                        نباشد.</p>
                </div>
                <div class="card-body">
                    <label for="election_template_paste" class="form-label">متن قالب</label>
                    <textarea class="form-control font-monospace small" id="election_template_paste" rows="8"
                        placeholder="بلوک [ELECTION_TEMPLATE] را اینجا بچسبانید"></textarea>
                    <div class="mt-2 d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-primary" id="election_template_apply">
                            اعمال روی فرم
                        </button>
                    </div>
                </div>
            </div>
        @endcan
    </form>
@endsection

@section('scripts')
    <script>
        window.__existingElectionTitles = @json($existingElectionTitles);
        window.__eventParticipantUserIds = @json($event->participants->pluck('user_id')->unique()->values()->all());

        function suggestDuplicateTitleClient(baseTitle, titles) {
            const set = new Set((titles || []).map(function(t) {
                return String(t).trim();
            }));
            const base = String(baseTitle || '').trim() || 'همه‌پرسی';
            let candidate = base + ' (کپی)';
            let n = 2;
            while (set.has(candidate)) {
                candidate = base + ' (کپی ' + n + ')';
                n += 1;
            }
            return candidate;
        }

        function parseElectionTemplateBlock(raw) {
            const m = String(raw || '').match(/\[ELECTION_TEMPLATE\]([\s\S]*?)\[\/ELECTION_TEMPLATE\]/i);
            if (!m) return null;
            const body = m[1];
            const out = {};
            body.split(/\r?\n/).forEach(function(line) {
                line = line.trim();
                if (!line || line.startsWith('#')) return;
                const idx = line.indexOf('=');
                if (idx === -1) return;
                const key = line.slice(0, idx).trim();
                const val = line.slice(idx + 1).trim();
                out[key] = val;
            });
            if (!out.version || out.version !== '1') return null;
            return out;
        }

        function applyElectionTemplateFromText() {
            const raw = document.getElementById('election_template_paste');
            if (!raw) return;
            const parsed = parseElectionTemplateBlock(raw.value);
            if (!parsed) {
                showToast('error', 'قالب شناسایی نشد. بلوک [ELECTION_TEMPLATE] … [/ELECTION_TEMPLATE] را کامل بچسبانید.');
                return;
            }
            const titles = window.__existingElectionTitles || [];
            const allowed = new Set((window.__eventParticipantUserIds || []).map(Number));

            const title = suggestDuplicateTitleClient(parsed.title || '', titles);
            document.getElementById('title').value = title;

            const typeEl = document.getElementById('election_type');
            if (typeEl && parsed.type) {
                const opt = Array.prototype.slice.call(typeEl.options).some(function(o) {
                    return o.value === parsed.type;
                });
                if (opt) {
                    typeEl.value = parsed.type;
                    if (window.jQuery && jQuery(typeEl).data('select2')) {
                        jQuery(typeEl).val(parsed.type).trigger('change');
                    }
                } else {
                    showToast('error', 'نوع همه‌پرسی داخل قالب با گزینه‌های این گروه سازگار نیست؛ نوع را دستی انتخاب کنید.');
                }
            }

            const posEl = document.getElementById('position_id');
            if (posEl && parsed.position_id) {
                const pid = String(parsed.position_id);
                const exists = Array.prototype.slice.call(posEl.options).some(function(o) {
                    return String(o.value) === pid;
                });
                if (exists) {
                    posEl.value = pid;
                    if (window.jQuery && jQuery(posEl).data('select2')) {
                        jQuery(posEl).val(pid).trigger('change');
                    }
                } else {
                    showToast('error', 'شناسهٔ مقام داخل قالب در فهرست مقام‌ها نیست؛ مقام را دستی انتخاب کنید.');
                }
            }

            if (parsed.main_member_count !== undefined) {
                const el = document.getElementById('main_member_count');
                if (el) el.value = parseInt(parsed.main_member_count, 10) || 1;
            }
            if (parsed.substitute_member_count !== undefined) {
                const el = document.getElementById('substitute_member_count');
                if (el) el.value = parseInt(parsed.substitute_member_count, 10) || 0;
            }

            const blockedEl = document.querySelector('select[name="blocked_user_ids[]"]');
            if (blockedEl && parsed.blocked_user_ids !== undefined) {
                const ids = String(parsed.blocked_user_ids || '')
                    .split(',')
                    .map(function(s) {
                        return parseInt(s.trim(), 10);
                    })
                    .filter(function(id) {
                        return !isNaN(id) && allowed.has(id);
                    });
                if (window.jQuery && jQuery(blockedEl).data('select2')) {
                    jQuery(blockedEl).val(ids.map(String)).trigger('change');
                } else {
                    Array.prototype.forEach.call(blockedEl.options, function(o) {
                        o.selected = ids.indexOf(parseInt(o.value, 10)) !== -1;
                    });
                }
            }

            validateCandidateCounts();
            showToast('success', 'قالب روی فرم اعمال شد؛ عنوان برای جلوگیری از تکرار تنظیم شد.');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('election_template_apply');
            if (btn) btn.addEventListener('click', applyElectionTemplateFromText);
        });

        function checkElectionType(event) {}

        function validateElectionCreateSubmit(e) {
            const posEl = document.getElementById('position_id');
            if (!posEl) {
                return true;
            }
            const raw = String(posEl.value || '').trim();
            if (raw === '') {
                showToast('error', 'لطفاً «مقام انتخاباتی» را انتخاب کنید.');
                e.preventDefault();
                return false;
            }
            if (!/^\d+$/.test(raw)) {
                showToast('error',
                    'مقام انتخاباتی باید شناسهٔ عددی باشد. اگر مقام جدید ساختید، صبر کنید تا «ایجاد مقام» تمام شود؛ یا از فهرست یک مقام موجود انتخاب کنید.');
                e.preventDefault();
                return false;
            }
            if (!validateCandidateCounts()) {
                showToast('error', 'تعداد اعضای اصلی باید بیشتر از تعداد اعضای علی‌البدل باشد.');
                e.preventDefault();
                return false;
            }
            return true;
        }

        $("#blocked_user_ids").select2({
            dir: "rtl",
            width: "100%",
            placeholder: "کاربرانی را انتخاب کنید",
            allowClear: true,
        });

        $("#position_id").select2({
            tags: true,
            createTag: function(params) {
                return {
                    id: params.term,
                    text: params.term,
                    newOption: true
                }
            },
            insertTag: function(data, tag) {
                if (tag.newOption) {
                    data.push(tag);
                }
            }
        }).on('select2:select', function(e) {
            const selectedData = e.params.data;

            if (selectedData.newOption) {
                const newPositionTitle = selectedData.text;

                createNewPosition(newPositionTitle);
            }
        });

        function createNewPosition(positionTitle) {
            const selectElement = $('#position_id');
            selectElement.prop('disabled', true);

            const formData = new FormData();
            formData.append('title', positionTitle);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route('positions.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        $('#position_id').find('option[value="' + positionTitle + '"]').remove();

                        const newOption = new Option(positionTitle, data.position_id, true, true);
                        $('#position_id').append(newOption).trigger('change');

                        showToast('success', 'مقام جدید با موفقیت ایجاد شد');
                    } else {
                        throw new Error(data.message || 'ایجاد مقام انجام نشد');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);

                    $('#position_id').find('option[value="' + positionTitle + '"]').remove();
                    $('#position_id').val('').trigger('change');

                    showToast('error', error.message || 'خطا در ایجاد مقام جدید');
                })
                .finally(() => {
                    selectElement.prop('disabled', false);
                });
        }

        function showToast(type, message) {
            if (typeof Toast !== 'undefined') {
                Toast.create({
                    title: type === 'success' ? 'موفق' : 'خطا',
                    message: message,
                    type: type,
                    duration: 3000,
                });
            } else {
                alert(message);
            }
        }

        function validateCandidateCounts() {
            const mainMemberCount = parseInt(document.getElementById('main_member_count').value, 10) || 0;
            const substituteMemberCount = parseInt(document.getElementById('substitute_member_count').value, 10) || 0;

            const infoDiv = document.getElementById('candidate-info');
            const infoText = document.getElementById('candidate-info-text');

            if (mainMemberCount === 0 && substituteMemberCount === 0) {
                infoDiv.style.display = 'none';
                return true;
            }

            const totalSeats = mainMemberCount + substituteMemberCount;

            if (mainMemberCount <= substituteMemberCount) {
                infoText.textContent =
                    `تعداد اعضای اصلی (${mainMemberCount}) باید بیشتر از تعداد اعضای علی‌البدل (${substituteMemberCount}) باشد.`;
                infoDiv.className = 'alert alert-danger';
                infoDiv.style.display = 'block';
                return false;
            }

            infoText.textContent =
                `صندلی‌های انتخابی: ${mainMemberCount} عضو اصلی + ${substituteMemberCount} علی‌البدل (مجموع ذخیره‌شده در سیستم به‌عنوان «تعداد نامزد» برابر ${totalSeats} است).`;
            infoDiv.className = 'alert alert-info';
            infoDiv.style.display = 'block';
            return true;
        }

        // فراخوانی اولیه برای نمایش وضعیت
        document.addEventListener('DOMContentLoaded', function() {
            validateCandidateCounts();
        });
    </script>
@endsection
