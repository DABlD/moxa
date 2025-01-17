<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Billing, Device, Reading};
use DB;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class BillingController extends Controller
{
    public function __construct(){
        $this->table = "billings";
    }

    public function index(){
        return $this->_view('index', [
            'title' => 'Billings'
        ]);
    }

    public function get(Request $req){
        $array = Billing::select($req->select);

        // IF HAS SORT PARAMETER $ORDER
        if($req->order){
            $array = $array->orderBy($req->order[0], $req->order[1]);
        }

        // IF HAS WHERE
        if($req->where){
            $array = $array->where($req->where[0], $req->where[1]);
        }

        // IF HAS LIKE
        if($req->like){
            $array = $array->where($req->like[0], 'LIKE' ,$req->like[1]);
        }

        $array = $array->get();

        // IF HAS LOAD
        if($array->count() && $req->load){
            foreach($req->load as $table){
                $array->load($table);
            }
        }

        // IF HAS GROUP
        if($req->group){
            $array = $array->groupBy($req->group);
        }

        echo json_encode($array);
    }

    public function store(Request $req){
        $device = Device::find($req->moxa_id);

        // $readings = Reading::where('moxa_id', $req->moxa_id)->whereBetween('datetime', [$req->from . ' 00:00:00', $req->to . ' 11:59:59'])->orderBy('datetime', 'desc')->get();
        $readings = Reading::where('moxa_id', $req->moxa_id)->orderBy('datetime', 'desc')->first();

        $bill = new Billing();
        $bill->user_id = $device->name;
        $bill->moxa_id = $req->moxa_id;
        $bill->billno = "MB" . now()->format('Ymd') . sprintf('%06d', Billing::count() + 1);
        $bill->reading = $req->reading;
        $bill->initReading = $readings->total;
        $bill->consumption = $req->reading - $bill->initReading;
        $bill->from = $req->from;
        $bill->to = $req->to;
        $bill->rate = $device->category->rate;
        $bill->late_interest = $device->category->late_interest;
        $bill->total = $bill->consumption * $device->category->rate;
        $bill->status = "Unpaid";
        $bill->save();
    }

    public function createBillings(Request $req){
        // building: $("#building").val(),
        // trueBuilding: $("#trueBuilding").val(), // DEVICE
        // from: $("#from").val(),
        // to: $("#to").val(),

        $devices = Device::where('category_id', 'like', $req->building)->where('id', 'like', $req->trueBuilding)->get();
        $devices->load('category');

        foreach($devices as $device){
            $readings = Reading::where('moxa_id', $device->id)->orderBy('datetime', 'desc')->take(2)->get();

            $bill = new Billing();

            if($readings->count() == 0){
                continue;
            }
            elseif($readings->count() == 1){
                $bill->reading = $readings->first()->total;
                $bill->initReading = 0;
            }
            elseif($readings->count() == 2){
                $bill->reading = $readings->first()->total;
                $bill->initReading = $readings->last()->total;
            }

            $bill->user_id = $device->name;
            $bill->moxa_id = $device->id;
            $bill->billno = "MB" . now()->format('Ymd') . sprintf('%06d', Billing::count() + 1);
            $bill->from = $req->from;
            $bill->to = $req->to;
            $bill->rate = $device->category->rate;
            $bill->late_interest = $device->category->late_interest;
            $bill->status = "Unpaid";
            $bill->consumption = $bill->reading - $bill->initReading;
            $bill->total = $bill->consumption * $device->category->rate;
            $bill->save();
        }
    }

    public function sendBilling(Request $req){
        $billing = Billing::find($req->id);
        $billing->load('device.user');

        require base_path("vendor/autoload.php");

        $mail = new PHPMailer(true);     // Passing `true` enables exceptions
        try {
            // Email server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';             //  smtp host
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USERNAME');   //  sender username
            $mail->Password = env('MAIL_PASSWORD');       // sender password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // encryption - ssl/tls
            $mail->Port = 587;                          // port - 587/465

            $mail->setFrom('info@onehealthnetwork.com.ph', 'AMR NO REPLY');
            $mail->addAddress($billing->device->user->email);
            $mail->addAddress("darm.111220@gmail.com");

            $mail->isHTML(true);                // Set email content format to HTML

            $mail->Subject = "AMR Billing";

            $mail->Body    = view('billings.email', ['billing' => $billing])->render();

            if( !$mail->send() ) {
                echo "Email sending failed";
            }
            
            else {
                echo "
                    <script>
                        window.alert('Email sent successfully. Please check your email');
                        window.close();
                    </script>
                ";
            }

        } catch (Exception $e) {
            dd($e->errorMessage());
            echo "Error. Email not sent";
        }
    }

    public function pay(Request $req){
        $bill = Billing::find($req->id);
        $bill->mop = $req->mop;
        $bill->refno = $req->refno;
        $bill->invoice = "INV" . now()->format('Ymd') . sprintf('%06d', Billing::where('invoice', 'like', "INV" . now()->format('Ymd') . '%')->count() + 1);
        $bill->status = "Paid";
        $bill->date_paid = now();
        $bill->save();
    }

    public function update(Request $req){
        $query = DB::table($this->table);

        if($req->where){
            $query = $query->where($req->where[0], $req->where[1])->update($req->except(['id', '_token', 'where']));
        }
        else{
            $query = $query->where('id', $req->id)->update($req->except(['id', '_token']));
        }
    }

    public function delete(Request $req){
        Billing::find($req->id)->delete();
    }

    private function _view($view, $data = array()){
        return view('billings' . "." . $view, $data);
    }
}
