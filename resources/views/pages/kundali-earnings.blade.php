@extends('../layout/' . $layout)

@section('subhead')
    <title>Kundali Earnings</title>
@endsection

@section('subcontent')
    @php
        $currency = DB::table('systemflag')
            ->where('name', 'Currency')
            ->select('value')
            ->first();
    @endphp
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10 d-inline">Kundali Earnings</h2>
    @if ($totalRecords > 0)
        <a data-tw-toggle="modal" data-tw-target="#add-skill"
            class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn printpdf">PDF</a>
        <a class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn downloadcsv">CSV</a>
    @endif
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <form action="{{ route('kundaliearning') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="w-56 relative text-slate-500" style="display:inline-block">
                        <input value="{{ $searchString }}" type="text" class="form-control w-56 box pr-10"
                            placeholder="Search..." id="searchString" name="searchString">
                        @if (!$searchString)
                            <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                        @else
                            <a href="{{ route('kundaliearning') }}"><i
                                    class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="x"></i></a>
                        @endif
                    </div>
                    <button class="btn btn-primary shadow-md mr-2">Search</button>
                </form>
            </div>
        </div>
    </div>
    <!-- BEGIN: Data List -->
    @if ($totalRecords > 0)
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible list-table">
            <table class="table table-report -mt-2" aria-label="call-history">
                <thead class="sticky-top">
                    <tr>
                        <th class="whitespace-nowrap">#</th>
                        <th class="text-center whitespace-nowrap">USER TYPE</th>
                        <th class="text-center whitespace-nowrap">USER</th>
                        <th class="text-center whitespace-nowrap">DATE</th>
                        <th class="text-center whitespace-nowrap">KUNDALI TYPE</th>
                        <th class="text-center whitespace-nowrap">COST</th>
                        <th class="text-center whitespace-nowrap">PDF</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 0;
                    @endphp
                    @foreach ($kundaliEarnings as $req)
                        <tr class="intro-x">
                            <td>{{ ($page - 1) * 15 + ++$no }}</td>

                            <td class="text-center"> 
                                {{$req->user_type}}
                            </td>
                            <td class="text-center">
                                {{ $req->userName }} - {{ $req->userContactNo }}
                            </td>
                           

                            <td class="text-center">
                                {{ date('d-m-Y', strtotime($req->created_at)) ? date('d-m-Y ', strtotime($req->created_at)) : '--' }}
                            </td>
                            <td class="text-center">
                                {{ $req->kundaliType }}
                            </td>
                            <td class="text-center">
                                @if($req->kundaliType=='small')
                                20
                                @elseif($req->kundaliType=='medium')
                                30
                                @elseif($req->kundaliType=='large')
                                49
                                @endif
                            </td>

                            @if($req->pdf_link!=null)
                            <td class="text-center">
                                <div class="flex justify-center items-center">
                                    
                                        <a class="flex items-center mr-3 text-success" href="{{url('public/'.$req->pdf_link)}}"
                                            target="_blank">
                                            <i data-lucide="download" class="w-4 h-4 mr-1"></i>DownloadFile
                                        </a>
                                </div>
                            </td>
                            @else
                            <td class="text-center">
                              No Pdf Available
                            </td>
                            @endif

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of
            {{ $totalRecords }} entries</div>
        <div class="d-inline addbtn intro-y col-span-12">
            <nav class="w-full sm:w-auto sm:mr-auto">
                <ul class="pagination" id="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link"
                            href="{{ route('kundaliearning', ['page' => $page - 1, 'searchString' => $searchString]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link"
                                href="{{ route('kundaliearning', ['page' => $i + 1, 'searchString' => $searchString]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link"
                            href="{{ route('kundaliearning', ['page' => $page + 1, 'searchString' => $searchString]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    @else
        <div class="intro-y mt-5" style="height:100%">
            <div style="display:flex;align-items:center;height:100%;">
                <div style="margin:auto">
                    <img src="/build/assets/images/nodata.png" style="height:290px" alt="noData">
                    <h3 class="text-center">No Data Available</h3>
                </div>
            </div>
        </div>
    @endif
   
    <!-- BEGIN: Delete Confirmation Modal -->
    <!-- END: Delete Confirmation Modal -->
@endsection
@section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"  ></script>

    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>
@endsection
