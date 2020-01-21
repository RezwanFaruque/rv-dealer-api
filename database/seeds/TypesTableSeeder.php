<?php

use App\Type;
use Illuminate\Database\Seeder;

class TypesTableSeeder extends Seeder
{

    private $data = [
        2 => 'Class A',
        3 =>'Class C',
        4 => 'Fifth Wheel',
        5 => 'Travel Trailer',
        6 => 'Popup',
        7 => 'Truck Camper',
        8 => 'Miscellaneous',
        9 => 'Class B',
        10 => 'Bus Conversion',
        11 => 'Park Model',
        12 => 'Boat',
        15 => 'Toy Hauler',
        16 => 'Diesel Pusher',
    ];
    private $order = [
        1 => 'Bus Conversion',
        2 => 'Diesel Pusher',
        3 => 'Class A',
        4 => 'Class C',
        5 => 'Class B',
        6 => 'Fifth Wheel',
        7 => 'Travel Trailer',
        8 => 'Popup',
        9 => 'Truck Camper',
        10 => 'Toy Hauler',
        11 => 'Boat',
        12 => 'Miscellaneous'
    ];

    /**
     * Run the database seeds.
     */
    public function run()
    {
        foreach ($this->data as $id => $class) {
            Type::create([
                'id' => $id,
                'name' => $class
            ]);
        }
        foreach ($this->order as $order=> $type){
            $type = Type::where('name' , $type)->first();
            if($type)
            {
                $type->order = $order;
                $type->save();
            }
        }

    }
}
