@extends('../layout/' . $layout)

@section('subhead')
    <title>Settings</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <form method="POST" enctype="multipart/form-data" id="edit-form">
        @csrf
        <h2 class="d-inline intro-y text-lg font-medium mt-10">Settings</h2>
        <button type="submit"class="btn btn-primary shadow-md mr-2 d-inline addbtn mt-10">Save
        </button>
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-12 overflow-auto lg:overflow-visible ">
            </div>
        </div>
        <div id="link-tab" class="p-3">
            <ul class="nav nav-link-tabs" role="tablist">
                @foreach ($flagGroup as $group)
                    <li id="{{ $loop->index }}" class="nav-item flex-1 {{ $loop->first ? 'active' : '' }}"
                        role="presentation">
                        <button class="nav-link w-full py-2 {{ $loop->first ? 'active' : '' }}" data-tw-toggle="pill"
                            data-tw-target="#{{ $group->flagGroupName }}" type="button" role="tab"
                            aria-controls="{{ $group->flagGroupName }}" aria-selected="true">
                            {{ $group->flagGroupName }}
                        </button>
                    </li>
                @endforeach
            </ul>
            <div class="setting tab-content mt-5 mastertab">
                @foreach ($flagGroup as $groupIndex => $group)
                    <div id="{{ $group->flagGroupName }}"
                        class="tab-pane leading-relaxed {{ $loop->first ? 'active' : '' }}" role="tabpanel"
                        aria-labelledby="example-1-tab">
                        @if (count($group->systemFlag) > 0)
                            @foreach ($group->systemFlag as $systemFlagIndex => $systemFlag)
                                @if ($systemFlag->valueType == 'Text')
                                    <div>
                                        <label for="validation-form-2"
                                            class="form-label w-full flex flex-col sm:flex-row mt-2">
                                            {{ $systemFlag->displayName }}
                                            @if ($systemFlag->description)
                                                <a class="systooltip"><i class="fa fa-info-circle w-4 h-4 ml-1"
                                                        style="margin-top:4px"></i>
                                                    <span class="tooltiptext">{{ $systemFlag->description }}</span>
                                                </a>
                                            @endif
                                        </label>
                                        <input type="hidden"
                                            name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][name]"
                                            value="{{ $systemFlag->name }}">
                                        <input onkeypress="return validateJavascript(event);" type="text"
                                            name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value]"
                                            class="form-control" value="{{ $systemFlag->value }}">
                                    </div>
                                @endif
                                @if ($systemFlag->valueType == 'Number')
                                    <div>
                                        <label for="validation-form-2"
                                            class="form-label w-full flex flex-col sm:flex-row mt-2">
                                            {{ $systemFlag->displayName }}
                                            @if ($systemFlag->description)
                                                <a class="systooltip"><i class="fa fa-info-circle w-4 h-4 ml-1"
                                                        style="margin-top:4px"></i>
                                                    <span class="tooltiptext">{{ $systemFlag->description }}</span>
                                                </a>
                                            @endif
                                        </label>
                                        <input type="hidden"
                                            name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][name]"
                                            value="{{ $systemFlag->name }}">
                                        <input type="number"
                                            name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value]"
                                            class="form-control" required value="{{ $systemFlag->value }}">
                                    </div>
                                @endif
                                @if ($systemFlag->valueType == 'Radio')
                                    <div>
                                        <label for="validation-form-2"
                                            class="form-label w-full flex flex-col sm:flex-row mt-2">
                                            {{ $systemFlag->displayName }}
                                            @if ($systemFlag->description)
                                                <a class="systooltip"><i class="fa fa-info-circle w-4 h-4 ml-1"
                                                        style="margin-top:4px"></i>
                                                    <span class="tooltiptext">{{ $systemFlag->description }}</span>
                                                </a>
                                            @endif
                                        </label>
                                        <input type="hidden"
                                            name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][name]"
                                            value="{{ $systemFlag->name }}">

                                        @if ($systemFlag->name == 'FirstFreeChat')
                                            <div class="flex flex-col sm:flex-row mt-2">
                                                <div class="form-check mr-2">
                                                    <input class="form-check-input" type="radio"
                                                        name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value]"
                                                        value='1'
                                                        {{ $systemFlag->value == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="radio-switch-4">Yes</label>
                                                </div>
                                                <div class="form-check mr-2 mt-2 sm:mt-0">
                                                    <input class="form-check-input" type="radio"
                                                        name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value]"
                                                        value='0' {{ $systemFlag->value == '0' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="radio-switch-5">No</label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                @if ($systemFlag->valueType == 'File')
                                    <div class="intro-y col-span-12 sm:col-span-6 2xl:col-span-4 xl:col-span-4  d-inline">
                                        <div class="box p-5  mt-2 text-center">
                                            <label for="validation-form-2" class="form-label w-full  mt-2">
                                                {{ $systemFlag->displayName }}
                                            </label>
                                            <input type="hidden"
                                                name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][valueType]"
                                                value="{{ $systemFlag->valueType }}">
                                            <input type="hidden"
                                                name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][name]"
                                                value="{{ $systemFlag->name }}">
                                            <div class="settingimg">
                                                <img id="{{ $systemFlag->name }}" src="/{{ $systemFlag->value }}"
                                                    width="150px" alt="gift">
                                            </div>
                                            <div>
                                                <input type="file" class="mt-2"
                                                    name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value]"
                                                    id="image" onchange="previews('{{ $systemFlag->name }}')"
                                                    accept="image/*">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if ($systemFlag->valueType == 'MultiSelect')
                                    <div>
                                        <label for="validation-form-2"
                                            class="form-label w-full flex flex-col sm:flex-row mt-2">
                                            {{ $systemFlag->displayName }}
                                            @if ($systemFlag->description)
                                                <a class="systooltip"><i class="fa fa-info-circle w-4 h-4 ml-1"
                                                        style="margin-top:4px"></i>
                                                    <span class="tooltiptext">{{ $systemFlag->description }}</span>
                                                </a>
                                            @endif
                                        </label>
                                        <input type="hidden"
                                            name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][name]"
                                            value="{{ $systemFlag->name }}">
                                        <input type="hidden"
                                            name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][valueType]"
                                            value="{{ $systemFlag->valueType }}">
                                        <select
                                            name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value][]"
                                            class="form-control select2 language" multiple
                                            data-placeholder="Choose Language">
                                            @foreach ($language as $lan)
                                                <option value={{ $lan->id }}>
                                                    {{ $lan->languageName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                @if ($systemFlag->valueType == 'Video')
                                    <div>
                                        <label for="image"
                                            class="form-label mt-2">{{ $systemFlag->displayName }}</label>
                                        <input type="hidden"
                                            name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][valueType]"
                                            value="{{ $systemFlag->valueType }}">
                                        <input type="hidden"
                                            name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][name]"
                                            value="{{ $systemFlag->name }}">
                                        <video controls id="editMyVideo" style="width:150px;object-fit:cover"
                                            preload="metadata">
                                            <source id="blogvideo" type="video/mp4" src="/{{ $systemFlag->value }}">
                                            <track label="English" kind="subtitles" srclang="en" default />
                                        </video>
                                        <input type="file" id="blogImage"
                                            name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value]"
                                            onchange="editVideoPreviews('{{ $systemFlag->name }}',{{ $loop->index }})"
                                            accept="video/mp4">
                                    </div>
                                @endif
                            @endforeach
                        @endif
                        @if (count($group->subGroup) > 0)
                            @foreach ($group->subGroup as $subGroupIndex => $subGroup)

                                <h4 class="my-4 text-lg font-medium"> {{ ucwords($subGroup->flagGroupName) }}
                                    @if ($subGroup->description)
                                        <a class="systooltip"><i class="fa fa-info-circle w-4 h-4 ml-1"
                                                style="margin-top:4px"></i>
                                            <span class="tooltiptext">{{ $subGroup->description }}</span>
                                        </a>
                                    @endif     
                                </h4>
                                @if($subGroup->parentFlagGroupId==2)
                                <div class="mb-2">
                                    <input type="hidden" value="{{$subGroup->id}}" name="flaggroups[{{$subGroup->id}}][id]">
                                    <label>
                                        <input class="form-check-input" type="radio" name="flaggroups[{{$subGroup->id}}][isActive]" value="1" {{ $subGroup->isActive ? 'checked' : '' }}>
                                        Enable
                                    </label>
                                    <label>
                                        <input class="form-check-input" type="radio" name="flaggroups[{{$subGroup->id}}][isActive]" value="0" {{ !$subGroup->isActive ? 'checked' : '' }}>
                                        Disable
                                    </label>
                                </div>
                                
                                @endif
                                <div class="box p-3">
                                    @if($subGroup->flagGroupName == "AstrologyAPI")
                                        <select name="astroApiCallType" id="astroApiCallType">
                                            {{-- <option value="1" {{ $astroApiCallType == 1 ? 'selected' : '' }}>Manual</option>
                                            <option value="2" {{ $astroApiCallType == 2 ? 'selected' : '' }}>Astrolger API</option> --}}
                                            <option value="3" {{ $astroApiCallType == 3 ? 'selected' : '' }}>Vedic Astro API</option>
                                        </select>
                                    @endif
                                    @foreach ($subGroup->systemFlag as $systemFlagInd => $systemFlag)
                                    @if ($systemFlag->valueType == 'Text')
                                    @if ($systemFlag->name != 'AstrologyApiUserId' && $systemFlag->name != 'AstrologyApiKey')
                                        <div>
                                            <label for="validation-form-2" class="form-label w-full flex flex-col sm:flex-row mt-2">
                                                {{ $systemFlag->displayName }}
                                            </label>
                                            <input type="hidden" name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][name]" value="{{ $systemFlag->name }}">
                                            <input onkeypress="return validateJavascript(event);" type="text" name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][value]" class="form-control" value="{{ $systemFlag->value }}">
                                        </div>
                                    @endif
                                @endif
                                
                                        @if ($systemFlag->valueType == 'Number')
                                            <div>
                                                <label for="validation-form-2"
                                                    class="form-label w-full flex flex-col sm:flex-row mt-2">
                                                    {{ $systemFlag->displayName }}
                                                </label>
                                                <input type="hidden"
                                                    name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][name]"
                                                    value="{{ $systemFlag->name }}">
                                                <input type="number"
                                                    name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][value]"
                                                    class="form-control" required value="{{ $systemFlag->value }}">
                                            </div>
                                        @endif
                                        @if ($systemFlag->valueType == 'Radio')
                                            <div>
                                                <label for="validation-form-2"
                                                    class="form-label w-full flex flex-col sm:flex-row mt-2">
                                                    {{ $systemFlag->displayName }}
                                                </label>
                                                <input type="hidden"
                                                    name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][name]"
                                                    value="{{ $systemFlag->name }}">

                                                <div class="flex flex-col sm:flex-row mt-2">
                                                    <div class="form-check mr-2">
                                                        <input class="form-check-input" type="radio"
                                                            name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][value]"
                                                            value='RazorPay'
                                                            {{ $systemFlag->value == 'RazorPay' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="radio-switch-4">Razor
                                                            Pay</label>
                                                    </div>
                                                    <div class="form-check mr-2 mt-2 sm:mt-0">
                                                        <input class="form-check-input" type="radio"
                                                            name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][value]"
                                                            value='Stripe'
                                                            {{ $systemFlag->value == 'Stripe' ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="radio-switch-5">Stripe</label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($systemFlag->valueType == 'File')
                                            <div
                                                class="intro-y col-span-12 sm:col-span-6 2xl:col-span-4 xl:col-span-4 d-inline">
                                                <div class="box p-5  mt-2 text-center">
                                                    <label for="validation-form-2" class="form-label w-full mt-2">
                                                        {{ $systemFlag->displayName }}
                                                    </label>
                                                    <input type="hidden"
                                                        name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][valueType]"
                                                        value="{{ $systemFlag->valueType }}">
                                                    <input type="hidden"
                                                        name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][name]"
                                                        value="{{ $systemFlag->name }}">
                                                    <div class="settingimg">
                                                        <img id="{{ $systemFlag->name }}"src="/{{ $systemFlag->value }}"
                                                            width="150px" alt="gift">
                                                    </div>
                                                    <div>
                                                        <input type="file" class="mt-2"
                                                            name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][value]"
                                                            id="image"
                                                            onchange="previews('{{ $systemFlag->name }}')"
                                                            accept="image/*">
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($systemFlag->valueType == 'Video')
                                            <div>
                                                <label for="image"
                                                    class="form-label mt-2">{{ $systemFlag->displayName }}</label>
                                                <input type="hidden"
                                                    name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][valueType]"
                                                    value="{{ $systemFlag->valueType }}">
                                                <input type="hidden"
                                                    name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][name]"
                                                    value="{{ $systemFlag->name }}">
                                                <video controls id="editMyVideo" style="width:150px;object-fit:cover"
                                                    preload="metadata">
                                                    <source id="blogvideo" type="video/mp4"
                                                        src="/{{ $systemFlag->value }}">
                                                    <track label="English" kind="subtitles" srclang="en" default />
                                                </video>
                                                <input type="file" id="blogImage"
                                                    name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][value]"
                                                    onchange="editVideoPreviews('{{ $systemFlag->name }}',{{ $loop->index }})"
                                                    accept="video/mp4">
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endforeach

            </div>
        </div>
    </form>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"  ></script>
    <script type="text/javascript">
        $(document).ready(function() {
            jQuery('.select2').select2({
                allowClear: true,
                tags: true,
                tokenSeparators: [',', ' ']
            });
        });

        var flagGroup = {{ Js::from($flagGroup) }};
        language = flagGroup.filter(c => c.flagGroupName == 'General');
        language = language[0].systemFlag.filter(c => c.name == 'Language')
        languageKnown = language[0].value.split(',')
        $('.language').val(languageKnown).trigger('change');


        function previews(id) {
            document.getElementById(id).src = URL.createObjectURL(event.target.files[0]);
        }

        function editVideoPreviews(id, index) {
            document.getElementById("editMyVideo").style.display = "block";
            blogvideo.src = URL.createObjectURL(event.target.files[0]);
            editMyVideo.load();
            editMyVideo.onended = function() {
                URL.revokeObjectURL(editMyVideo.currentSrc);
            };
            document.getElementById("editMyVideo").controls = true;

        }
    </script>
    <script>
        var spinner = $('.loader');
        jQuery(function() {
            jQuery('#edit-form').submit(function(e) {
                e.preventDefault();
                spinner.show();
                var data = new FormData(this);
                
                jQuery.ajax({
                    type: 'POST',
                    url: "{{ route('editSystemFlag') }}",
                    data: data,
                    dataType: 'JSON',
                    processData: false,
                    contentType: false,
                    success: function(data) {
                     
                        if (jQuery.isEmptyObject(data.error)) {
                            spinner.hide();
                            location.reload();
                        } else {
                            spinner.hide();
                        }
                    }
                });
            });
        });

        $(window).on('load', function() {
            $('.loader').hide();
        });

        function validateJavascript(event) {
            var regex = new RegExp("^[<>]");
            var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
            if (regex.test(key)) {
                event.preventDefault();
                return false;
            }
        }
    </script>
@endsection
