@extends('layouts.admin.app')

@section('title', 'Cabang Utama')

@push('css_or_js')
    <style>
        #location_map_div #pac-input{
            height: 40px;
            border: 1px solid #fbc1c1;
            outline: none;
            box-shadow: none;
            top: 7px !important;
            transform: translateX(7px);
            padding-left: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/business_setup2.png')}}" alt="">
                <span class="page-header-title">
                    Cabang Utama
                </span>
            </h2>
        </div>

        @include('admin-views.business-settings.partials._business-setup-inline-menu')

        <div class="row g-2">
            <div class="col-12">
                <form action="{{route('admin.branch.update', ['id' => $branch['id']])}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0 d-flex gap-2 align-items-center">
                                <i class="tio-user"></i>
                                Informasi Cabang
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">Name</label>
                                        <input value="{{$branch['name']}}" type="text" name="name" class="form-control" maxlength="255" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="input-label" for="">Alamat</label>
                                        <input value="{{$branch['address']}}" type="text" name="address" class="form-control" placeholder="" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="input-label">Waktu Persiapan (Menit)<span class="text-danger ml-1">*</span>
                                            <i class="tio-info-outined"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               title="Waktu persiapan akan ditampilkan dipelanggan">
                                            </i>
                                        </label>
                                        <input value="{{ $branch['preparation_time'] }}" type="number" name="preparation_time" class="form-control"
                                               placeholder="Contoh : 30" min="1" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <label class="mb-0">Foto Cabang</label>
                                            <small class="text-danger">* ( Rasio 1:1 )</small>
                                        </div>

                                        <div class="d-flex justify-content-center mt-4">
                                            <div class="upload-file">
                                                <input type="file" name="image" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" class="upload-file__input">
                                                <div class="upload-file__img_drag upload-file__img">
                                                    <img width="150" src="{{$branch->imageFullPath}}" alt="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="input-label">No. Hp</label>
                                        <input value="{{$branch['phone']}}" type="tel" name="phone" class="form-control"
                                               placeholder="Contoh : 081234xxxxx" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="input-label">Email</label>
                                        <input value="{{$branch['email']}}" type="email" name="email" class="form-control" maxlength="255"
                                               placeholder="Contoh : cabang1@gmail.com" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="input-label">Password <span class="text-danger">(*Masukkan jika ingin mengatur ulang password)</span></label>
                                        <div class="input-group input-group-merge">
                                            <input type="password" name="password" class="js-toggle-password form-control form-control input-field" id="password"
                                                   placeholder="Password"
                                                   data-hs-toggle-password-options='{
                                                "target": "#changePassTarget",
                                                "defaultClass": "tio-hidden-outlined",
                                                "showClass": "tio-visible-outlined",
                                                "classChangeTarget": "#changePassIcon"
                                                }'>
                                            <div id="changePassTarget" class="input-group-append">
                                                <a class="input-group-text" href="javascript:">
                                                    <i id="changePassIcon" class="tio-visible-outlined"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @php($googleMapStatus = \App\CentralLogics\Helpers::get_business_settings('google_map_status'))
                    @if($googleMapStatus)
                        <div class="card mt-3">
                            <div class="card-header">
                                <h4 class="mb-0 d-flex gap-2 align-items-center">
                                    <i class="tio-map"></i>
                                    Lokasi Toko
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <div class="form-group mb-0">
                                                    <label class="form-label text-capitalize"
                                                           for="latitude">Latitude
                                                        <i class="tio-info-outined"
                                                           data-toggle="tooltip"
                                                           data-placement="top"
                                                           title="Klik pada peta untuk pilih lokasi default anda">
                                                        </i>
                                                    </label>
                                                    <input type="text" id="latitude" name="latitude" class="form-control"
                                                           placeholder="Contoh : 23.8118428"
                                                           value="{{ $branch['latitude'] }}" required >
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group mb-0">
                                                    <label class="form-label text-capitalize"
                                                           for="longitude">Longitude
                                                        <i class="tio-info-outined"
                                                           data-toggle="tooltip"
                                                           data-placement="top"
                                                           title="Klik pada peta untuk pilih lokasi default anda">
                                                        </i>
                                                    </label>
                                                    <input type="text" name="longitude" class="form-control"
                                                           placeholder="Contoh : 90.356331" id="longitude"
                                                           value="{{ $branch['longitude'] }}" required>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group mb-0">
                                                    <label class="input-label">
                                                        Cakupan (KM)
                                                        <i class="tio-info-outined"
                                                           data-toggle="tooltip"
                                                           data-placement="top"
                                                           title="Nilai ini adalah radius dari lokasi toko anda">
                                                        </i>

                                                    </label>
                                                    <input type="number" name="coverage" min="1" max="1000" class="form-control" placeholder="Contoh : 3" value="{{ $branch['coverage'] }}" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6" id="location_map_div">
                                        <input id="pac-input" class="controls rounded" data-toggle="tooltip"
                                               data-placement="right"
                                               data-original-title="Cari lokasi anda disini"
                                               type="text" placeholder="Cari disini" />
                                        <div id="location_map_canvas" class="overflow-hidden rounded" style="height: 100%"></div>
                                    </div>

                                </div>


                            </div>
                        </div>
                    @endif
                    <div class="d-flex justify-content-end gap-3 mt-4">
                        <button type="reset" class="btn btn-secondary">Atur Ulang</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

@endsection

@push('script_2')

    <script src="https://maps.googleapis.com/maps/api/js?key={{ \App\Models\BusinessSetting::where('key', 'map_api_client_key')->first()?->value }}&libraries=places&v=3.45.8"></script>

    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });


        $( document ).ready(function() {
            function initAutocomplete() {
                var myLatLng = {

                    lat: {{$branch['latitude'] ?? 23.811842872190343}},
                    lng: {{$branch['longitude'] ??  90.356331}},
                };
                const map = new google.maps.Map(document.getElementById("location_map_canvas"), {
                    center: {
                        lat: {{$branch['latitude'] ?? 23.811842872190343}},
                        lng: {{$branch['longitude'] ?? 90.356331}},
                    },
                    zoom: 13,
                    mapTypeId: "roadmap",
                });

                var marker = new google.maps.Marker({
                    position: myLatLng,
                    map: map,
                });

                marker.setMap(map);
                var geocoder = geocoder = new google.maps.Geocoder();
                google.maps.event.addListener(map, 'click', function(mapsMouseEvent) {
                    var coordinates = JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2);
                    var coordinates = JSON.parse(coordinates);
                    var latlng = new google.maps.LatLng(coordinates['lat'], coordinates['lng']);
                    marker.setPosition(latlng);
                    map.panTo(latlng);

                    document.getElementById('latitude').value = coordinates['lat'];
                    document.getElementById('longitude').value = coordinates['lng'];


                    geocoder.geocode({
                        'latLng': latlng
                    }, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            if (results[1]) {
                                document.getElementById('address').innerHtml = results[1].formatted_address;
                            }
                        }
                    });
                });
                // Create the search box and link it to the UI element.
                const input = document.getElementById("pac-input");
                const searchBox = new google.maps.places.SearchBox(input);
                map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
                // Bias the SearchBox results towards current map's viewport.
                map.addListener("bounds_changed", () => {
                    searchBox.setBounds(map.getBounds());
                });
                let markers = [];
                // Listen for the event fired when the user selects a prediction and retrieve
                // more details for that place.
                searchBox.addListener("places_changed", () => {
                    const places = searchBox.getPlaces();

                    if (places.length == 0) {
                        return;
                    }
                    // Clear out the old markers.
                    markers.forEach((marker) => {
                        marker.setMap(null);
                    });
                    markers = [];
                    // For each place, get the icon, name and location.
                    const bounds = new google.maps.LatLngBounds();
                    places.forEach((place) => {
                        if (!place.geometry || !place.geometry.location) {
                            console.log("Returned place contains no geometry");
                            return;
                        }
                        var mrkr = new google.maps.Marker({
                            map,
                            title: place.name,
                            position: place.geometry.location,
                        });
                        google.maps.event.addListener(mrkr, "click", function(event) {
                            document.getElementById('latitude').value = this.position.lat();
                            document.getElementById('longitude').value = this.position.lng();
                        });

                        markers.push(mrkr);

                        if (place.geometry.viewport) {
                            // Only geocodes have viewport.
                            bounds.union(place.geometry.viewport);
                        } else {
                            bounds.extend(place.geometry.location);
                        }
                    });
                    map.fitBounds(bounds);
                });
            };

            initAutocomplete();

        });
    </script>

@endpush
