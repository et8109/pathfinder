<html>
    <head>
        <style>
            body{
                background-color: black;
                overflow: scroll;
                color: grey;
            }
            h1{
                color:#7f7f7f;
                text-align: center;
            }
            #main{
                margin-left: auto;
                margin-right: auto;
                margin-top: 50px;
                width: 200px;
                border-radius: 25px;
                background-color: grey;
                text-align: center;
                padding-top: 15px;
                padding-bottom: 20px;
            }
            #logout{
                /*display: none;*/
            }
            #options{
                /*display: none;*/
            }
            #compass{
                visibility: hidden;
                color: white;
                position: absolute;
                margin-top: 160px;
                border: solid 1px white;
                width: 20px;
                /*-webkit-transition: margin-left .3s;
                transition: margin-left .3s;*/
            }
            #log{
                color: #b9b9b9;
                position: absolute;
            }
        </style>
    </head>
    <body>
        <?php
            function handle($e){
                //db exception
                if($e instanceof dbException){
                    if($e->getCode() == dbException::CODE_COULD_NOT_CONNECT){
                        echo $e->getMessage();
                    }
                //normal exception
                } else{
                    echo "general exception:";
                    echo $e->getMessage();
                }
            }
            set_exception_handler("handle");
        ?>