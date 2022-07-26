@extends('voyager::master')

@section('content')
    <style>
        h3{
            margin-bottom: 20px;
        }
.gauge{
    transition: background 1s ease;
    font-size: 30px;
    line-height: 30px;
    padding: 30px;
    border-radius: 8px;
    border: 3px solid #f9f9f9;
    display: inline-block;
    font-weight: bold;
}
.gauge-init{
    background-color: #C3C3C3;
    border: 3px solid #C3C3C3;
    color: #00003A;
}

.gauge-ok{
    background-color: #B5E61D;

}
.gauge-nok{
    background-color: #ED1C24;
    color: #fff;
}
        .gear{
            display: inline-block;
            vertical-align: bottom;
        }
        .gaugebtn-in,.gaugebtn-out{
            cursor: pointer;
        }
        .gear-animate{
            display: none;
        }

        #RESET_UPLOADED{
            padding:15px 30px;
        }

        #RESET_UPLOADED:hover{
            cursor: pointer;
        }

        .store-seperator{
            border:2px solid #000;
            margin:1rem 0;
        }

        .each-store-action-label{
            font-size:30px;
            font-weight:bold;
            margin:0;
            text-align: center;

        }

    </style>
    <div class="container-fluid" style="color: #00003A;" >
        <h2><i class="voyager-eye" style="vertical-align: middle"></i> Supervision</h2>

         @foreach($stores as $k=>$v)

         <div class="col-12">
            <h3 class="text-center">{{$k}}</h3>

    @if($v[0]['code']=='AT')

        <div class="row">
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="row">
                        <h3 class="text-center">ASSEMBLY CONVEYOR</h3>
                            <div class="row">
                            <div class="col-lg-2"><div class="text-center"><div class="gauge">IN</div></div></div>
                                <div class="col-lg-10 text-center"><div class="gauge gauge-init" id="AT_MAPEXC_IN">--</div></div>

                            </div>
                            <div class="row">
                                <div class="col-lg-2"><div class="text-center"><div class="gauge">OUT</div></div></div>
                                <div class="col-lg-10 text-center"><div class="gauge gauge-init"  id="AT_MAPEXC_OUT">--</div></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="row">
                        <h3 class="text-center">STORAGE CONVEYOR</h3>
                            <div class="row">
                                <div class="col-lg-12 text-center"><div class="gauge gauge-init"  id="AT_BAMEXC_IN">--</div></div>
                                <div class="col-lg-12 text-center"><div class="gauge gauge-init"  id="AT_BAMEXC_OUT">--</div></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-4">
                <h3 class="text-center">DATA</h3>
                <div class="row">
                    <!--<h3 class="text-center">Item No Sent</h3>-->
                    <div class="row">
                        <div class="col-lg-4 text-center"><div class="gauge gauge-init">--</div></div>
                        <div class="col-lg-8 text-center"><div class="gauge gauge-init gaugebtn-in" data-store-code="{{$stores[$k][0]['code']}}" ><svg class="gear gear-static" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="30px" height="30px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" style="background-color:rgba(255, 255, 255,0);animation-play-state:paused" ><g transform="translate(50 50)" style="animation-play-state:paused" ><g transform="matrix(1,0,0,1,0,0)" style="animation-play-state:paused" ><path d="M33.46640106136302 -6 L41.46640106136302 -6 L41.46640106136302 6 L33.46640106136302 6 A34 34 0 0 1 27.906959819517752 19.421678445279177 L27.906959819517752 19.421678445279177 L33.563814069010135 25.078532694771557 L25.07853269477156 33.56381406901013 L19.42167844527918 27.90695981951775 A34 34 0 0 1 6.000000000000006 33.46640106136302 L6.000000000000006 33.46640106136302 L6.000000000000006 41.46640106136302 L-6.000000000000003 41.46640106136302 L-6.000000000000003 33.46640106136302 A34 34 0 0 1 -19.421678445279174 27.906959819517756 L-19.421678445279174 27.906959819517756 L-25.078532694771553 33.563814069010135 L-33.563814069010135 25.07853269477156 L-27.906959819517752 19.421678445279177 A34 34 0 0 1 -33.46640106136302 6.000000000000008 L-33.46640106136302 6.000000000000008 L-41.46640106136302 6.000000000000009 L-41.46640106136302 -5.999999999999999 L-33.46640106136302 -6 A34 34 0 0 1 -27.906959819517756 -19.421678445279174 L-27.906959819517756 -19.421678445279174 L-33.563814069010135 -25.078532694771553 L-25.078532694771575 -33.56381406901012 L-19.42167844527919 -27.90695981951774 A34 34 0 0 1 -5.999999999999996 -33.46640106136302 L-5.999999999999996 -33.46640106136302 L-5.999999999999997 -41.46640106136302 L5.999999999999981 -41.46640106136302 L5.999999999999983 -33.46640106136302 A34 34 0 0 1 19.42167844527918 -27.90695981951775 L19.42167844527918 -27.90695981951775 L25.07853269477156 -33.56381406901013 L33.56381406901012 -25.078532694771575 L27.90695981951774 -19.421678445279195 A34 34 0 0 1 33.46640106136302 -5.999999999999997 M0 -24A24 24 0 1 0 0 24 A24 24 0 1 0 0 -24" fill="#00003a" style="animation-play-state:paused" ></path></g></g><!-- generated by https://loading.io/ --></svg>
                                <svg class="gear gear-animate" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background: rgba(255, 255, 255,0) none repeat scroll 0% 0%; shape-rendering: auto; animation-play-state: running; animation-delay: 0s;" width="30px" height="30px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                                    <g transform="translate(50 50)" style="animation-play-state: running; animation-delay: 0s;">
                                        <g style="animation-play-state: running; animation-delay: 0s;">
                                            <animateTransform attributeName="transform" type="rotate" values="0;45" keyTimes="0;1" dur="0.2s" repeatCount="indefinite" style="animation-play-state: running; animation-delay: 0s;"></animateTransform><path d="M33.46640106136302 -6 L41.46640106136302 -6 L41.46640106136302 6 L33.46640106136302 6 A34 34 0 0 1 27.906959819517752 19.421678445279177 L27.906959819517752 19.421678445279177 L33.563814069010135 25.078532694771557 L25.07853269477156 33.56381406901013 L19.42167844527918 27.90695981951775 A34 34 0 0 1 6.000000000000006 33.46640106136302 L6.000000000000006 33.46640106136302 L6.000000000000006 41.46640106136302 L-6.000000000000003 41.46640106136302 L-6.000000000000003 33.46640106136302 A34 34 0 0 1 -19.421678445279174 27.906959819517756 L-19.421678445279174 27.906959819517756 L-25.078532694771553 33.563814069010135 L-33.563814069010135 25.07853269477156 L-27.906959819517752 19.421678445279177 A34 34 0 0 1 -33.46640106136302 6.000000000000008 L-33.46640106136302 6.000000000000008 L-41.46640106136302 6.000000000000009 L-41.46640106136302 -5.999999999999999 L-33.46640106136302 -6 A34 34 0 0 1 -27.906959819517756 -19.421678445279174 L-27.906959819517756 -19.421678445279174 L-33.563814069010135 -25.078532694771553 L-25.078532694771575 -33.56381406901012 L-19.42167844527919 -27.90695981951774 A34 34 0 0 1 -5.999999999999996 -33.46640106136302 L-5.999999999999996 -33.46640106136302 L-5.999999999999997 -41.46640106136302 L5.999999999999981 -41.46640106136302 L5.999999999999983 -33.46640106136302 A34 34 0 0 1 19.42167844527918 -27.90695981951775 L19.42167844527918 -27.90695981951775 L25.07853269477156 -33.56381406901013 L33.56381406901012 -25.078532694771575 L27.90695981951774 -19.421678445279195 A34 34 0 0 1 33.46640106136302 -5.999999999999997 M0 -24A24 24 0 1 0 0 24 A24 24 0 1 0 0 -24" fill="#00003a" style="animation-play-state: running; animation-delay: 0s;"></path></g></g>
                                    <!-- [ldio] generated by https://loading.io/ --></svg>
                                Manual</div>
                    </div>
                </div>

                    <div class="row">
                        <div class="col-lg-4 text-center">
                            <div class="gauge gauge-init gauge-nok" id="AT_RESET_UPLOADED">
                                <svg class="gear gear-static" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="30px" height="30px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" style="background-color:rgba(255, 255, 255,0);animation-play-state:paused" ><g transform="translate(50 50)" style="animation-play-state:paused" ><g transform="matrix(1,0,0,1,0,0)" style="animation-play-state:paused" ><path d="M33.46640106136302 -6 L41.46640106136302 -6 L41.46640106136302 6 L33.46640106136302 6 A34 34 0 0 1 27.906959819517752 19.421678445279177 L27.906959819517752 19.421678445279177 L33.563814069010135 25.078532694771557 L25.07853269477156 33.56381406901013 L19.42167844527918 27.90695981951775 A34 34 0 0 1 6.000000000000006 33.46640106136302 L6.000000000000006 33.46640106136302 L6.000000000000006 41.46640106136302 L-6.000000000000003 41.46640106136302 L-6.000000000000003 33.46640106136302 A34 34 0 0 1 -19.421678445279174 27.906959819517756 L-19.421678445279174 27.906959819517756 L-25.078532694771553 33.563814069010135 L-33.563814069010135 25.07853269477156 L-27.906959819517752 19.421678445279177 A34 34 0 0 1 -33.46640106136302 6.000000000000008 L-33.46640106136302 6.000000000000008 L-41.46640106136302 6.000000000000009 L-41.46640106136302 -5.999999999999999 L-33.46640106136302 -6 A34 34 0 0 1 -27.906959819517756 -19.421678445279174 L-27.906959819517756 -19.421678445279174 L-33.563814069010135 -25.078532694771553 L-25.078532694771575 -33.56381406901012 L-19.42167844527919 -27.90695981951774 A34 34 0 0 1 -5.999999999999996 -33.46640106136302 L-5.999999999999996 -33.46640106136302 L-5.999999999999997 -41.46640106136302 L5.999999999999981 -41.46640106136302 L5.999999999999983 -33.46640106136302 A34 34 0 0 1 19.42167844527918 -27.90695981951775 L19.42167844527918 -27.90695981951775 L25.07853269477156 -33.56381406901013 L33.56381406901012 -25.078532694771575 L27.90695981951774 -19.421678445279195 A34 34 0 0 1 33.46640106136302 -5.999999999999997 M0 -24A24 24 0 1 0 0 24 A24 24 0 1 0 0 -24" fill="#00003a" style="animation-play-state:paused" ></path></g></g><!-- generated by https://loading.io/ --></svg>
                                <svg class="gear gear-animate" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background: rgba(255, 255, 255,0) none repeat scroll 0% 0%; shape-rendering: auto; animation-play-state: running; animation-delay: 0s;" width="30px" height="30px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                                    <g transform="translate(50 50)" style="animation-play-state: running; animation-delay: 0s;">
                                        <g style="animation-play-state: running; animation-delay: 0s;">
                                            <animateTransform attributeName="transform" type="rotate" values="0;45" keyTimes="0;1" dur="0.2s" repeatCount="indefinite" style="animation-play-state: running; animation-delay: 0s;"></animateTransform><path d="M33.46640106136302 -6 L41.46640106136302 -6 L41.46640106136302 6 L33.46640106136302 6 A34 34 0 0 1 27.906959819517752 19.421678445279177 L27.906959819517752 19.421678445279177 L33.563814069010135 25.078532694771557 L25.07853269477156 33.56381406901013 L19.42167844527918 27.90695981951775 A34 34 0 0 1 6.000000000000006 33.46640106136302 L6.000000000000006 33.46640106136302 L6.000000000000006 41.46640106136302 L-6.000000000000003 41.46640106136302 L-6.000000000000003 33.46640106136302 A34 34 0 0 1 -19.421678445279174 27.906959819517756 L-19.421678445279174 27.906959819517756 L-25.078532694771553 33.563814069010135 L-33.563814069010135 25.07853269477156 L-27.906959819517752 19.421678445279177 A34 34 0 0 1 -33.46640106136302 6.000000000000008 L-33.46640106136302 6.000000000000008 L-41.46640106136302 6.000000000000009 L-41.46640106136302 -5.999999999999999 L-33.46640106136302 -6 A34 34 0 0 1 -27.906959819517756 -19.421678445279174 L-27.906959819517756 -19.421678445279174 L-33.563814069010135 -25.078532694771553 L-25.078532694771575 -33.56381406901012 L-19.42167844527919 -27.90695981951774 A34 34 0 0 1 -5.999999999999996 -33.46640106136302 L-5.999999999999996 -33.46640106136302 L-5.999999999999997 -41.46640106136302 L5.999999999999981 -41.46640106136302 L5.999999999999983 -33.46640106136302 A34 34 0 0 1 19.42167844527918 -27.90695981951775 L19.42167844527918 -27.90695981951775 L25.07853269477156 -33.56381406901013 L33.56381406901012 -25.078532694771575 L27.90695981951774 -19.421678445279195 A34 34 0 0 1 33.46640106136302 -5.999999999999997 M0 -24A24 24 0 1 0 0 24 A24 24 0 1 0 0 -24" fill="#00003a" style="animation-play-state: running; animation-delay: 0s;"></path></g></g>
                                    <!-- [ldio] generated by https://loading.io/ --></svg>Reset
                            </div>
                        </div>
                        <div class="col-lg-8 text-center"><div class="gauge gauge-init gaugebtn-out" data-store-code="{{$stores[$k][0]['code']}}"><svg class="gear gear-static" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="30px" height="30px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" style="background-color:rgba(255, 255, 255,0);animation-play-state:paused" ><g transform="translate(50 50)" style="animation-play-state:paused" ><g transform="matrix(1,0,0,1,0,0)" style="animation-play-state:paused" ><path d="M33.46640106136302 -6 L41.46640106136302 -6 L41.46640106136302 6 L33.46640106136302 6 A34 34 0 0 1 27.906959819517752 19.421678445279177 L27.906959819517752 19.421678445279177 L33.563814069010135 25.078532694771557 L25.07853269477156 33.56381406901013 L19.42167844527918 27.90695981951775 A34 34 0 0 1 6.000000000000006 33.46640106136302 L6.000000000000006 33.46640106136302 L6.000000000000006 41.46640106136302 L-6.000000000000003 41.46640106136302 L-6.000000000000003 33.46640106136302 A34 34 0 0 1 -19.421678445279174 27.906959819517756 L-19.421678445279174 27.906959819517756 L-25.078532694771553 33.563814069010135 L-33.563814069010135 25.07853269477156 L-27.906959819517752 19.421678445279177 A34 34 0 0 1 -33.46640106136302 6.000000000000008 L-33.46640106136302 6.000000000000008 L-41.46640106136302 6.000000000000009 L-41.46640106136302 -5.999999999999999 L-33.46640106136302 -6 A34 34 0 0 1 -27.906959819517756 -19.421678445279174 L-27.906959819517756 -19.421678445279174 L-33.563814069010135 -25.078532694771553 L-25.078532694771575 -33.56381406901012 L-19.42167844527919 -27.90695981951774 A34 34 0 0 1 -5.999999999999996 -33.46640106136302 L-5.999999999999996 -33.46640106136302 L-5.999999999999997 -41.46640106136302 L5.999999999999981 -41.46640106136302 L5.999999999999983 -33.46640106136302 A34 34 0 0 1 19.42167844527918 -27.90695981951775 L19.42167844527918 -27.90695981951775 L25.07853269477156 -33.56381406901013 L33.56381406901012 -25.078532694771575 L27.90695981951774 -19.421678445279195 A34 34 0 0 1 33.46640106136302 -5.999999999999997 M0 -24A24 24 0 1 0 0 24 A24 24 0 1 0 0 -24" fill="#00003a" style="animation-play-state:paused" ></path></g></g><!-- generated by https://loading.io/ --></svg>
                                <svg class="gear gear-animate" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background: rgba(255, 255, 255,0) none repeat scroll 0% 0%; shape-rendering: auto; animation-play-state: running; animation-delay: 0s;" width="30px" height="30px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                                    <g transform="translate(50 50)" style="animation-play-state: running; animation-delay: 0s;">
                                        <g style="animation-play-state: running; animation-delay: 0s;">
                                            <animateTransform attributeName="transform" type="rotate" values="0;45" keyTimes="0;1" dur="0.2s" repeatCount="indefinite" style="animation-play-state: running; animation-delay: 0s;"></animateTransform><path d="M33.46640106136302 -6 L41.46640106136302 -6 L41.46640106136302 6 L33.46640106136302 6 A34 34 0 0 1 27.906959819517752 19.421678445279177 L27.906959819517752 19.421678445279177 L33.563814069010135 25.078532694771557 L25.07853269477156 33.56381406901013 L19.42167844527918 27.90695981951775 A34 34 0 0 1 6.000000000000006 33.46640106136302 L6.000000000000006 33.46640106136302 L6.000000000000006 41.46640106136302 L-6.000000000000003 41.46640106136302 L-6.000000000000003 33.46640106136302 A34 34 0 0 1 -19.421678445279174 27.906959819517756 L-19.421678445279174 27.906959819517756 L-25.078532694771553 33.563814069010135 L-33.563814069010135 25.07853269477156 L-27.906959819517752 19.421678445279177 A34 34 0 0 1 -33.46640106136302 6.000000000000008 L-33.46640106136302 6.000000000000008 L-41.46640106136302 6.000000000000009 L-41.46640106136302 -5.999999999999999 L-33.46640106136302 -6 A34 34 0 0 1 -27.906959819517756 -19.421678445279174 L-27.906959819517756 -19.421678445279174 L-33.563814069010135 -25.078532694771553 L-25.078532694771575 -33.56381406901012 L-19.42167844527919 -27.90695981951774 A34 34 0 0 1 -5.999999999999996 -33.46640106136302 L-5.999999999999996 -33.46640106136302 L-5.999999999999997 -41.46640106136302 L5.999999999999981 -41.46640106136302 L5.999999999999983 -33.46640106136302 A34 34 0 0 1 19.42167844527918 -27.90695981951775 L19.42167844527918 -27.90695981951775 L25.07853269477156 -33.56381406901013 L33.56381406901012 -25.078532694771575 L27.90695981951774 -19.421678445279195 A34 34 0 0 1 33.46640106136302 -5.999999999999997 M0 -24A24 24 0 1 0 0 24 A24 24 0 1 0 0 -24" fill="#00003a" style="animation-play-state: running; animation-delay: 0s;"></path></g></g>
                                    <!-- [ldio] generated by https://loading.io/ --></svg>
                                Manual</div>
                        </div>
                    </div>
            </div>


        </div><!--row-->

    @else
        @foreach($v as $index=>$conv)
            <div class="row">
                <div class="col-12">
                    <h4 class="text-center">{{$conv['name']}}</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-2">
                    <div class="row"><div class="col-lg-12"><h3 class="each-store-action-label">IN</h3></div></div>
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <div class="gauge gauge-init" id="{{$conv['code']}}_BAMEXC_IN">--</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2">
                    <div class="row"><div class="col-lg-12"><h3 class="each-store-action-label">OUT</h3></div></div>
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <div class="gauge gauge-init" id="{{$conv['code']}}_BAMEXC_OUT">--</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="row"><div class="col-lg-12"><h3 class="each-store-action-label">&nbsp;</h3></div></div>
                    <div class="gauge gauge-init gaugebtn-out" data-store-code="{{$conv['code']}}"><svg class="gear gear-static" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="30px" height="30px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" style="background-color:rgba(255, 255, 255,0);animation-play-state:paused" ><g transform="translate(50 50)" style="animation-play-state:paused" ><g transform="matrix(1,0,0,1,0,0)" style="animation-play-state:paused" ><path d="M33.46640106136302 -6 L41.46640106136302 -6 L41.46640106136302 6 L33.46640106136302 6 A34 34 0 0 1 27.906959819517752 19.421678445279177 L27.906959819517752 19.421678445279177 L33.563814069010135 25.078532694771557 L25.07853269477156 33.56381406901013 L19.42167844527918 27.90695981951775 A34 34 0 0 1 6.000000000000006 33.46640106136302 L6.000000000000006 33.46640106136302 L6.000000000000006 41.46640106136302 L-6.000000000000003 41.46640106136302 L-6.000000000000003 33.46640106136302 A34 34 0 0 1 -19.421678445279174 27.906959819517756 L-19.421678445279174 27.906959819517756 L-25.078532694771553 33.563814069010135 L-33.563814069010135 25.07853269477156 L-27.906959819517752 19.421678445279177 A34 34 0 0 1 -33.46640106136302 6.000000000000008 L-33.46640106136302 6.000000000000008 L-41.46640106136302 6.000000000000009 L-41.46640106136302 -5.999999999999999 L-33.46640106136302 -6 A34 34 0 0 1 -27.906959819517756 -19.421678445279174 L-27.906959819517756 -19.421678445279174 L-33.563814069010135 -25.078532694771553 L-25.078532694771575 -33.56381406901012 L-19.42167844527919 -27.90695981951774 A34 34 0 0 1 -5.999999999999996 -33.46640106136302 L-5.999999999999996 -33.46640106136302 L-5.999999999999997 -41.46640106136302 L5.999999999999981 -41.46640106136302 L5.999999999999983 -33.46640106136302 A34 34 0 0 1 19.42167844527918 -27.90695981951775 L19.42167844527918 -27.90695981951775 L25.07853269477156 -33.56381406901013 L33.56381406901012 -25.078532694771575 L27.90695981951774 -19.421678445279195 A34 34 0 0 1 33.46640106136302 -5.999999999999997 M0 -24A24 24 0 1 0 0 24 A24 24 0 1 0 0 -24" fill="#00003a" style="animation-play-state:paused" ></path></g></g><!-- generated by https://loading.io/ --></svg>
                                <svg class="gear gear-animate" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background: rgba(255, 255, 255,0) none repeat scroll 0% 0%; shape-rendering: auto; animation-play-state: running; animation-delay: 0s;" width="30px" height="30px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                                    <g transform="translate(50 50)" style="animation-play-state: running; animation-delay: 0s;">
                                        <g style="animation-play-state: running; animation-delay: 0s;">
                                            <animateTransform attributeName="transform" type="rotate" values="0;45" keyTimes="0;1" dur="0.2s" repeatCount="indefinite" style="animation-play-state: running; animation-delay: 0s;"></animateTransform><path d="M33.46640106136302 -6 L41.46640106136302 -6 L41.46640106136302 6 L33.46640106136302 6 A34 34 0 0 1 27.906959819517752 19.421678445279177 L27.906959819517752 19.421678445279177 L33.563814069010135 25.078532694771557 L25.07853269477156 33.56381406901013 L19.42167844527918 27.90695981951775 A34 34 0 0 1 6.000000000000006 33.46640106136302 L6.000000000000006 33.46640106136302 L6.000000000000006 41.46640106136302 L-6.000000000000003 41.46640106136302 L-6.000000000000003 33.46640106136302 A34 34 0 0 1 -19.421678445279174 27.906959819517756 L-19.421678445279174 27.906959819517756 L-25.078532694771553 33.563814069010135 L-33.563814069010135 25.07853269477156 L-27.906959819517752 19.421678445279177 A34 34 0 0 1 -33.46640106136302 6.000000000000008 L-33.46640106136302 6.000000000000008 L-41.46640106136302 6.000000000000009 L-41.46640106136302 -5.999999999999999 L-33.46640106136302 -6 A34 34 0 0 1 -27.906959819517756 -19.421678445279174 L-27.906959819517756 -19.421678445279174 L-33.563814069010135 -25.078532694771553 L-25.078532694771575 -33.56381406901012 L-19.42167844527919 -27.90695981951774 A34 34 0 0 1 -5.999999999999996 -33.46640106136302 L-5.999999999999996 -33.46640106136302 L-5.999999999999997 -41.46640106136302 L5.999999999999981 -41.46640106136302 L5.999999999999983 -33.46640106136302 A34 34 0 0 1 19.42167844527918 -27.90695981951775 L19.42167844527918 -27.90695981951775 L25.07853269477156 -33.56381406901013 L33.56381406901012 -25.078532694771575 L27.90695981951774 -19.421678445279195 A34 34 0 0 1 33.46640106136302 -5.999999999999997 M0 -24A24 24 0 1 0 0 24 A24 24 0 1 0 0 -24" fill="#00003a" style="animation-play-state: running; animation-delay: 0s;"></path></g></g>
                                    <!-- [ldio] generated by https://loading.io/ --></svg>
                                Manual</div>
                </div>



                <div class="col-lg-2">
                    <div class="row"><div class="col-lg-12"><h3 class="each-store-action-label">DATA</h3></div></div>
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <div class="gauge gauge-init" id="{{$conv['code']}}_NOT_SENT">--</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="row"><div class="col-lg-12"><h3 class="each-store-action-label">&nbsp;</h3></div></div>
                    <div class="gauge gauge-init gaugebtn-in" data-store-code="{{$conv['code']}}" ><svg class="gear gear-static" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="30px" height="30px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" style="background-color:rgba(255, 255, 255,0);animation-play-state:paused" ><g transform="translate(50 50)" style="animation-play-state:paused" ><g transform="matrix(1,0,0,1,0,0)" style="animation-play-state:paused" ><path d="M33.46640106136302 -6 L41.46640106136302 -6 L41.46640106136302 6 L33.46640106136302 6 A34 34 0 0 1 27.906959819517752 19.421678445279177 L27.906959819517752 19.421678445279177 L33.563814069010135 25.078532694771557 L25.07853269477156 33.56381406901013 L19.42167844527918 27.90695981951775 A34 34 0 0 1 6.000000000000006 33.46640106136302 L6.000000000000006 33.46640106136302 L6.000000000000006 41.46640106136302 L-6.000000000000003 41.46640106136302 L-6.000000000000003 33.46640106136302 A34 34 0 0 1 -19.421678445279174 27.906959819517756 L-19.421678445279174 27.906959819517756 L-25.078532694771553 33.563814069010135 L-33.563814069010135 25.07853269477156 L-27.906959819517752 19.421678445279177 A34 34 0 0 1 -33.46640106136302 6.000000000000008 L-33.46640106136302 6.000000000000008 L-41.46640106136302 6.000000000000009 L-41.46640106136302 -5.999999999999999 L-33.46640106136302 -6 A34 34 0 0 1 -27.906959819517756 -19.421678445279174 L-27.906959819517756 -19.421678445279174 L-33.563814069010135 -25.078532694771553 L-25.078532694771575 -33.56381406901012 L-19.42167844527919 -27.90695981951774 A34 34 0 0 1 -5.999999999999996 -33.46640106136302 L-5.999999999999996 -33.46640106136302 L-5.999999999999997 -41.46640106136302 L5.999999999999981 -41.46640106136302 L5.999999999999983 -33.46640106136302 A34 34 0 0 1 19.42167844527918 -27.90695981951775 L19.42167844527918 -27.90695981951775 L25.07853269477156 -33.56381406901013 L33.56381406901012 -25.078532694771575 L27.90695981951774 -19.421678445279195 A34 34 0 0 1 33.46640106136302 -5.999999999999997 M0 -24A24 24 0 1 0 0 24 A24 24 0 1 0 0 -24" fill="#00003a" style="animation-play-state:paused" ></path></g></g><!-- generated by https://loading.io/ --></svg>
                                <svg class="gear gear-animate" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background: rgba(255, 255, 255,0) none repeat scroll 0% 0%; shape-rendering: auto; animation-play-state: running; animation-delay: 0s;" width="30px" height="30px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                                    <g transform="translate(50 50)" style="animation-play-state: running; animation-delay: 0s;">
                                        <g style="animation-play-state: running; animation-delay: 0s;">
                                            <animateTransform attributeName="transform" type="rotate" values="0;45" keyTimes="0;1" dur="0.2s" repeatCount="indefinite" style="animation-play-state: running; animation-delay: 0s;"></animateTransform><path d="M33.46640106136302 -6 L41.46640106136302 -6 L41.46640106136302 6 L33.46640106136302 6 A34 34 0 0 1 27.906959819517752 19.421678445279177 L27.906959819517752 19.421678445279177 L33.563814069010135 25.078532694771557 L25.07853269477156 33.56381406901013 L19.42167844527918 27.90695981951775 A34 34 0 0 1 6.000000000000006 33.46640106136302 L6.000000000000006 33.46640106136302 L6.000000000000006 41.46640106136302 L-6.000000000000003 41.46640106136302 L-6.000000000000003 33.46640106136302 A34 34 0 0 1 -19.421678445279174 27.906959819517756 L-19.421678445279174 27.906959819517756 L-25.078532694771553 33.563814069010135 L-33.563814069010135 25.07853269477156 L-27.906959819517752 19.421678445279177 A34 34 0 0 1 -33.46640106136302 6.000000000000008 L-33.46640106136302 6.000000000000008 L-41.46640106136302 6.000000000000009 L-41.46640106136302 -5.999999999999999 L-33.46640106136302 -6 A34 34 0 0 1 -27.906959819517756 -19.421678445279174 L-27.906959819517756 -19.421678445279174 L-33.563814069010135 -25.078532694771553 L-25.078532694771575 -33.56381406901012 L-19.42167844527919 -27.90695981951774 A34 34 0 0 1 -5.999999999999996 -33.46640106136302 L-5.999999999999996 -33.46640106136302 L-5.999999999999997 -41.46640106136302 L5.999999999999981 -41.46640106136302 L5.999999999999983 -33.46640106136302 A34 34 0 0 1 19.42167844527918 -27.90695981951775 L19.42167844527918 -27.90695981951775 L25.07853269477156 -33.56381406901013 L33.56381406901012 -25.078532694771575 L27.90695981951774 -19.421678445279195 A34 34 0 0 1 33.46640106136302 -5.999999999999997 M0 -24A24 24 0 1 0 0 24 A24 24 0 1 0 0 -24" fill="#00003a" style="animation-play-state: running; animation-delay: 0s;"></path></g></g>
                                    <!-- [ldio] generated by https://loading.io/ --></svg>
                                Manual</div>

                </div>



                <!--
                <div class="col-lg-1"><div class="text-center"><div class="gauge">IN</div></div></div>
                <div class="col-lg-2 text-center"><div class="gauge gauge-init" id="{{$conv['code']}}_BAMEXC_IN">--</div></div>
                <div class="col-lg-1"><div class="text-center"><div class="gauge">OUT</div></div></div>
                <div class="col-lg-2 text-center"><div class="gauge gauge-init" id="{{$conv['code']}}_BAMEXC_OUT">--</div></div>
                -->
            </div>
        @endforeach
    @endif

        </div>
        <div class="col-12 store-seperator"></div>

        @endforeach


        <div class="row">
            <div class="col-lg-12 text-center"><h3>LOG</h3></div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Id</th><th>Code</th><th>Conveyor type</th><th>File type</th><th>Message</th><th>Date</th></tr></thead>
                    <tbody id="ftplog">

                    </tbody>

                </table>
            </div>
        </div>
    </div>
@endsection

@section('javascript')

    <script type="text/javascript">
        function http(url,method,store) {

            return new Promise(
                function (resolve, reject) {
                    var request = new XMLHttpRequest();

                    request.onreadystatechange = function () {
                        if (this.readyState === 4) {                            // ***
                            if (this.status === 200) {
                                // Success
                                resolve(this.response);
                            } else {
                                // Something went wrong (404 etc.)
                                reject(new Error(this.statusText));
                            }
                        }                                                       // ***
                    }
                    request.onerror = function () {
                        reject(new Error(
                            'XMLHttpRequest Error: '+this.statusText));
                    };


                    request.open(method, url);
                    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                    request.send('store='+store);
                });
        }

        let url = '/admin/supervisiondata';

        let getSupervisonData=()=>{
            http(url,'GET','')
                .then(JSON.parse)
                .then((r) => {
                   $('.gauge').removeClass('gauge-ok');
                    $('.gauge').removeClass('gauge-nok');

                    $('#AT_MAPEXC_IN').html(r.MAPEXC_IN);
                    if(r.MAPEXC_IN==0) {
                        $('#AT_MAPEXC_IN').addClass('gauge-ok');
                    }else{
                        $('#AT_MAPEXC_IN').addClass('gauge-nok');
                    }

                    $('#AT_MAPEXC_OUT').html(r.MAPEXC_OUT);
                    if(r.MAPEXC_OUT==0) {
                        $('#AT_MAPEXC_OUT').addClass('gauge-ok');
                    }else{
                        $('#AT_MAPEXC_OUT').addClass('gauge-nok');
                    }

                    $.each(r.BAMEXC_IN,function(i,v){
                        $('#'+i+'_BAMEXC_IN').html(v);
                        if(v==0){
                            $('#'+i+'_BAMEXC_IN').addClass('gauge-ok');
                        }else{
                            $('#'+i+'_BAMEXC_IN').addClass('gauge-nok');
                        }
                    });

                    $.each(r.BAMEXC_OUT,function(i,v){
                        $('#'+i+'_BAMEXC_OUT').html(v);
                        if(v==0){
                            $('#'+i+'_BAMEXC_OUT').addClass('gauge-ok');
                        }else{
                            $('#'+i+'_BAMEXC_OUT').addClass('gauge-nok');
                        }
                    });

                    $.each(r.NUM_ITEM,function(i,v){
                        $('#'+i+'_NOT_SENT').html(v);
                        if(v==0){
                            $('#'+i+'_NOT_SENT').addClass('gauge-ok');
                        }else{
                            $('#'+i+'_NOT_SENT').addClass('gauge-nok');
                        }
                    });


                    /*
                    $('#AT_BAMEXC_IN').html(r.BAMEXC_IN);
                    if(r.BAMEXC_IN==0) {
                        $('#AT_BAMEXC_IN').addClass('gauge-ok');
                    }else{
                        $('#AT_BAMEXC_IN').addClass('gauge-nok');
                    }

                    $('#AT_BAMEXC_OUT').html(r.BAMEXC_OUT);
                    if(r.BAMEXC_OUT==0) {
                        $('#AT_BAMEXC_OUT').addClass('gauge-ok');
                    }else{
                        $('#AT_BAMEXC_OUT').addClass('gauge-nok');
                    }

                    $('#AT_NUM_ITEM').html(r.NUM_ITEM);
                    if(r.NUM_ITEM==0) {
                        $('#AT_NUM_ITEM').addClass('gauge-ok');
                    }else{
                        $('#AT_NUM_ITEM').addClass('gauge-nok');
                    }
                    */

                    //ftplog
                    let ftplog="";
                r.conveyor_file_log.forEach(function(val,index) {
                        ftplog+='<tr class="'+(val.err==1?'danger':'')+'"><td>'+val.id+'</td><td>'+val.ConvStoreCode+'</td><td>'+val.type_conveyor+'</td><td>'+val.type_file+'</td><td>'+val.detail+'</td><td>'+val.created_at+'</td></tr>';
                    })
                    $('#ftplog').html(ftplog);

                }).catch(function(error) {
                console.log(error);
            });
        }

        getSupervisonData();

        setInterval(function(){
            getSupervisonData();
        }, 10000);

        $(function () {
            $('.gaugebtn-in').click(function () {


               let store_code = $(this).attr('data-store-code');

                $('.gaugebtn-in').find('.gear-animate').show();
                $('.gaugebtn-in').find('.gear-static').hide();
                http('/conveyor-in?key=EjD4L7tgrHxmCY3exnCE31b3&store='+store_code,'GET','').finally( ()=>{
                    $('.gaugebtn-in').find('.gear-animate').hide();
                    $('.gaugebtn-in').find('.gear-static').show();
                });
            });
            $('.gaugebtn-out').click(function () {

                let store_code = $(this).attr('data-store-code');
                $('.gaugebtn-out').find('.gear-animate').show();
                $('.gaugebtn-out').find('.gear-static').hide();
                http('/conveyorout', 'POST',store_code).finally(() => {
                    $('.gaugebtn-out').find('.gear-animate').hide();
                    $('.gaugebtn-out').find('.gear-static').show();
                });
            });

            $('#RESET_UPLOADED').click(function(){
                $('#RESET_UPLOADED').find('.gear-animate').show();
                $('#RESET_UPLOADED').find('.gear-static').hide();

                http('/reset-uploaded-files?key=EjD4L7tgrHxmCY3exnCE31b3','GET')
                .then((res)=>{
                    console.log(res);
                }).catch((error)=>{

                }).finally(()=> {
                    $('#RESET_UPLOADED').find('.gear-animate').hide();
                    $('#RESET_UPLOADED').find('.gear-static').show();

                });
            });
        })
    </script>
@stop
