@extends('../layout/' . $layout)

@section('subhead')
    <title>Gifts</title>
@endsection

@section('subcontent')
    @php
        $currency = DB::table('systemflag')->where('name', 'Currency')->select('value')->first();
    @endphp
    <div class="loader"></div>
    <h2 class="d-inline intro-y text-lg font-medium mt-10">Stories</h2>

    <div class="grid grid-cols-12 gap-6">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        </div>
    </div>
    <!-- BEGIN: Data List -->
    @if (count($story) > 0)
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible withoutsearch">
            <table class="table table-report -mt-2" aria-label="gift">
                <thead class="sticky-top">
                    <tr>
                        <th class="whitespace-nowrap">#</th>

                        <th class="whitespace-nowrap">PROFILE</th>
                        <th class="text-center whitespace-nowrap">NAME</th>
                        <th class="text-center whitespace-nowrap">MEDIATYPE</th>
                        <th class="text-center whitespace-nowrap">MEDIA</th>
                        <th class="text-center whitespace-nowrap">VIEWS</th>
                        <th class="text-center whitespace-nowrap">CREATED AT</th>
                        <th class="text-center whitespace-nowrap">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 0;
                    @endphp
                    @foreach ($story as $stori)
                        <tr class="intro-x">
                            <td>{{ ($page - 1) * 15 + ++$no }} </td>
                            <td>
                                <div class="flex">
                                    <div class="w-10 h-10 image-fit zoom-in">
                                        <img class="rounded-full" src="/{{ $stori->profileImage }}"
                                            onerror="this.onerror=null;this.src='/build/assets/images/default.jpg';"
                                            alt="Astrologer image" />
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">{{ $stori->name }}</td>

                            <td class="text-center">{{ $stori->mediaType }}</td>
                            @if ($stori->mediaType == 'image')
                                <td class="text-center">
                                    <div class="flex " style="justify-content: center">
                                        <div class="w-10 h-10 image-fit zoom-in">
                                            <img class="rounded-full" src="/{{ $stori->media }}"
                                                onerror="this.onerror=null;this.src='/build/assets/images/default.jpg';"
                                                alt="Astrologer image" />
                                        </div>
                                    </div>
                                </td>
                            @elseif($stori->mediaType == 'video')
                                <td class="text-center">
                                    <video width="140" height="60" controls style="display: inline">
                                        <source src="/{{ $stori->media }}" type="video/mp4">
                                    </video>
                                </td>
                            @else
                                <td class="text-center font-medium">{{ $stori->media }}</td>
                            @endif
                            <td class="text-center text-success font-medium">{{$stori->StoryViewCount}}</td>
                            <td class="text-center">{{ $stori->created_at }}</td>

                            <td class="table-report__action w-56">
                                <div class="flex justify-center items-center">

                                    <a id="editbtn" href="javascript:;" onclick="delbtn({{ $stori['id'] }})"
                                        value="{{ $stori['id'] }}" class="flex items-center text-danger"
                                        data-tw-target="#delete-confirmation-modal" data-tw-toggle="modal"><i
                                            data-lucide="trash-2" class="editbtn w-4 h-4 mr-1"></i>Delete</a>
                                </div>
                            </td>


                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($totalRecords > 0)
            <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of
                {{ $totalRecords }} entries</div>
        @endif
        <div class="d-inline addbtn intro-y col-span-12">
            <nav class="w-full sm:w-auto sm:mr-auto">
                <ul class="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('story-list', ['page' => $page - 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link" href="{{ route('story-list', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('story-list', ['page' => $page + 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    @else
        <div class="intro-y" style="height:100%">
            <div style="display:flex;align-items:center;height:100%;">
                <div style="margin:auto">
                    <img src="/build/assets/images/nodata.png" style="height:290px" alt="noData">
                    <h3 class="text-center">No Data Available</h3>
                </div>
            </div>
        </div>
    @endif



    <!-- BEGIN: Delete Confirmation Modal -->
    <div id="delete-confirmation-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                        <div class="text-3xl mt-5">Are you sure?</div>
                        <div class="text-slate-500 mt-2">Do you really want to delete these records? <br>This process
                            cannot be undone.</div>
                    </div>

                    <form action="{{ route('deleteStory') }} " method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="del_id" name="del_id">
                        <div class="px-5 pb-8 text-center">
                            <button type="button" data-tw-dismiss="modal"
                                class="btn btn-outline-secondary w-24 mr-1">Cancel</button>
                            <button class="btn btn-danger w-24">@method('DELETE')Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- END: Delete Confirmation Modal -->
@endsection

@section('script')
    <script type="text/javascript">
        function delbtn($id, $name) {
            var id = $id;
            $did = id;

            $('#del_id').val($did);
            $('#id').val($id);
        }
    </script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>
@endsection
