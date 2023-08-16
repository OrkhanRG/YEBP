@extends('layouts.admin')
@section('title')
    Log Siyahısı
@endsection

@section('css')
    <link href="{{asset('assets/admin/plugins/flatpickr/flatpickr.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/admin/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
    <style>
        .table-hover > tbody > tr:hover {
            --bs-table-hover-bg: transparent;
            background: #363638;
            color: white;
        }
    </style>
@endsection

@section('content')
    <x-bootstrap.card>
        <x-slot:header>
            <h2>Məqalə Siyahısı</h2>
        </x-slot:header>
        <x-slot:body>
            <form action="">
                <div class="row">
                    <div class="col-3 my-2">
                        <input type="text" class="form-control" name="search_text" placeholder="Title, Slug, Body, Tags"
                               value="{{request()->get('search_text')}}">
                    </div>
                    <hr>
                    <div class="col-6 mb-3 d-flex">
                        <button type="submit" class="btn btn-primary w-50 me-4">Filtrlə</button>
                        <button type="submit" class="btn btn-warning w-50">Filtri Təmizlə</button>
                    </div>
                    <hr>
                </div>
            </form>
            <x-bootstrap.table
                :class="'table-stripped table-hover'"
                :is-responsive="1"
            >
                <x-slot:columns>
                    <th scope="col">Action</th>
                    <th scope="col">Model</th>
                    <th scope="col">Model View</th>
                    <th scope="col">User</th>
                    <th scope="col">Data</th>
                    <th scope="col">Created At</th>
                </x-slot:columns>

                <x-slot:rows>
                    @foreach($list as $log)

                        <tr id="row-{{$log->id}}">
                            <td>{{$log->action}}</td>
                            <td>{{$log->loggable_type}}</td>
                            <td>
                                <a href="javascript:void(0)"
                                   class="btn btn-info btn-sm btnLogDetail"
                                   data-bs-toggle="modal"
                                   data-bs-target="#contenViewModal"
                                   data-id="{{ $log->id }}"
                                   >
                                    <i class="material-icons ms-0">visibility</i>
                                </a>
                            </td>
                            <td>
                                {{ $log->user->name }}
                            </td>
                            <td>
                                <a href="javascript:void(0)"
                                   class="btn btn-primary btn-sm"
                                   data-bs-toggle="modal"
                                   data-bs-target="#contenViewModal"
                                >
                                    <i class="material-icons ms-0">visibility</i>
                                </a>
                            </td>
                            <td>{{ $log->created_at }}</td>
                        </tr>
                    @endforeach
                </x-slot:rows>
            </x-bootstrap.table>
            <div class="d-flex justify-content-center">
                {{--                {{$list->links('vendor.pagination.bootstrap-5')}}--}}
                {{$list->appends(request()->all())->onEachside(1)->links()}}

            </div>
        </x-slot:body>
    </x-bootstrap.card>

    <div class="modal fade" id="contenViewModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Log Info</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        $(Document).ready(function () {

            $('select').select2();

            $('.btnChangeStatus').click(function () {
                let articleID = $(this).data('id');
                let self = $(this);
                Swal.fire({
                    title: 'Statusu dəyişdirməy istədiyinizə əminsiz?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Hə',
                    denyButtonText: `Yox`,
                    cancelButtonText: `Ləvğ et`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed)
                    {
                        $.ajax({
                           method: "POST",
                            url: "{{  route('article.changeStatus') }}",
                            data: {
                                articleID: articleID
                            },
                            success: function (data){
                                if(data.article_status)
                                {
                                    self.removeClass('btn-danger');
                                    self.addClass('btn-success');
                                    self.text('Aktiv');
                                }
                                else
                                {
                                    self.removeClass('btn-success');
                                    self.addClass('btn-danger');
                                    self.text('Passiv');
                                }

                                Swal.fire({
                                    title: 'Uğurlu',
                                    confirmButtonText: 'yaxşı',
                                    text: 'Status dəyişdirildi',
                                    icon: 'success',
                                });
                            },
                            error: function () {
                                console.log('ERRORRRR');
                            }
                        })
                    }
                    else if (result.isDenied)
                    {
                        Swal.fire({
                            title: 'Info',
                            confirmButtonText: 'yaxşı',
                            text: 'Heçbir dəyişiklik edilmədi',
                            icon: 'info',
                        });
                    }
                })

            });

            $('.btnLogDetail').click(function () {
                let logID = $(this).data('id');
                let self = $(this);
                let route = "{{ route('dbLogs.getLog', ['id' => ':id']) }}";
                route = route.replace(":id", logID);
                $.ajax({
                    method: "get",
                    url: route,
                    async: false,
                    success: function (data) {
                        $('#modalBody').html(data);
                    },
                    error: function () {
                        console.log('ERRORRRR');
                    }
                })
            });

            $('.btnDelete').click(function () {
                let articleID = $(this).data('id');
                let articleName = $(this).data('name');
                let self = $(this);

                Swal.fire({
                    title: articleName + ' i Silmək istədiyinizə əminsiz?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Hə',
                    denyButtonText: `Yox`,
                    cancelButtonText: `Ləvğ et`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                       $.ajax({
                           method: 'POST',
                           url: "{{route('article.delete')}}",
                           data: {
                               '_method': 'DELETE',
                               articleID: articleID
                           },
                           async: false,
                           success: function (data){
                                $('#row-' + articleID).remove();

                               Swal.fire({
                                   title: 'Uğurlu',
                                   confirmButtonText: 'yaxşı',
                                   text: 'Məqalə silindi',
                                   icon: 'success',
                               });
                           },
                           error: function (){
                               console.log('ERRORRRR');
                           }
                       });

                    } else if (result.isDenied) {
                        Swal.fire({
                            title: 'Info',
                            confirmButtonText: 'yaxşı',
                            text: 'Heçbir dəyişiklik edilmədi',
                            icon: 'info',
                        });
                    }
                })

            });

        });
    </script>
    <script src="{{asset('assets/admin/plugins/flatpickr/flatpickr.js')}}"></script>
    <script src="{{asset('assets/admin/js/pages/datepickers.js')}}"></script>
    <script src="{{asset('assets/admin/plugins/select2/js/select2.full.min.js')}}"></script>
    <script src="{{asset('assets/admin/js/pages/select2.js')}}"></script>
    <script src="{{asset('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/admin/plugins/bootstrap/js/popper.min.js')}}"></script>

    <script>
        $("#publish_date").flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });

        const popover = new bootstrap.Popover('.example-popover', {
            container: 'body'
        })

    </script>
@endsection
