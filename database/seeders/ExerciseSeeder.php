<?php

namespace Database\Seeders;

use App\Models\Exercise;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        $exercises = [
            // CHEST (15 exercises)
            ['Barbell Bench Press', 'chest', 'triceps', 'barbell', 'strength', 'Baring di bangku datar, turunkan bar ke tengah dada dan dorong ke atas.'],
            ['Incline Barbell Bench Press', 'chest', 'shoulders', 'barbell', 'strength', 'Bench miring 30-45 derajat, fokus pada dada bagian atas.'],
            ['Decline Barbell Bench Press', 'chest', 'triceps', 'barbell', 'strength', 'Bench miring ke bawah, fokus pada dada bagian bawah.'],
            ['Dumbbell Bench Press', 'chest', 'triceps', 'dumbbell', 'strength', 'Dorong sepasang dumbbell ke atas dengan rentang gerak penuh.'],
            ['Incline Dumbbell Press', 'chest', 'shoulders', 'dumbbell', 'strength', 'Dumbbell press di bangku miring untuk aktivasi upper chest.'],
            ['Decline Dumbbell Press', 'chest', 'triceps', 'dumbbell', 'strength', 'Dumbbell press di bangku miring ke bawah.'],
            ['Dumbbell Flyes', 'chest', 'shoulders', 'dumbbell', 'strength', 'Regangkan dada secara isolasi dengan siku sedikit ditekuk.'],
            ['Incline Dumbbell Flyes', 'chest', 'shoulders', 'dumbbell', 'strength', 'Flyes pada bangku miring untuk isolasi dada atas.'],
            ['Cable Crossover', 'chest', 'shoulders', 'cable', 'strength', 'Tarik kabel dari atas menyilang ke bawah di depan pinggang.'],
            ['Low Cable Chest Fly', 'chest', 'shoulders', 'cable', 'strength', 'Tarik kabel dari posisi bawah ke atas menyilang di dada.'],
            ['Chest Dip (Forward Lean)', 'chest', 'triceps', 'bodyweight', 'strength', 'Dip di parallel bar dengan badan condong 30 derajat ke depan.'],
            ['Push Up', 'chest', 'core', 'bodyweight', 'strength', 'Push up klasik menjaga punggung lurus dan dada menyentuh lantai.'],
            ['Incline Push Up', 'chest', 'triceps', 'bodyweight', 'strength', 'Tangan di bangku atau platform lebih tinggi.'],
            ['Decline Push Up', 'chest', 'shoulders', 'bodyweight', 'strength', 'Kaki dinaikkan di atas bangku, fokus upper chest.'],
            ['Pec Deck Machine Fly', 'chest', 'shoulders', 'machine', 'strength', 'Isolasi otot dada dengan mesin pec deck.'],

            // BACK (16 exercises)
            ['Barbell Deadlift', 'back', 'legs', 'barbell', 'strength', 'Angkat barbel dari lantai dengan mengaktifkan seluruh rantai posterior tubuh.'],
            ['Conventional Barbell Row', 'back', 'biceps', 'barbell', 'strength', 'Condongkan badan 45 derajat dan tarik barbel ke arah pusar.'],
            ['Pendlay Row', 'back', 'biceps', 'barbell', 'strength', 'Barbell row dari lantai setiap repetisi, fokus kekuatan ledak lat.'],
            ['T-Bar Row', 'back', 'biceps', 'machine', 'strength', 'Tarik beban T-bar ke arah dada bawah dengan punggung stabil.'],
            ['Single-Arm Dumbbell Row', 'back', 'biceps', 'dumbbell', 'strength', 'Satu tangan dan lutut di bangku, tarik dumbbell ke arah pinggul.'],
            ['Lat Pulldown (Wide Grip)', 'back', 'biceps', 'cable', 'strength', 'Tarik bar pulldown ke arah dada bagian atas.'],
            ['Close Grip Lat Pulldown', 'back', 'biceps', 'cable', 'strength', 'Gunakan handle segitiga untuk aktivasi lat bawah.'],
            ['Seated Cable Row', 'back', 'biceps', 'cable', 'strength', 'Duduk tegak, tarik handle kabel ke perut sambil memeras belikat.'],
            ['Pull Up (Overhand)', 'back', 'biceps', 'bodyweight', 'strength', 'Tarik tubuh ke atas sampai dagu melewati tiang bar.'],
            ['Chin Up (Underhand)', 'back', 'biceps', 'bodyweight', 'strength', 'Genggaman telapak tangan menghadap wajah, aktivasi lats dan bisep.'],
            ['Face Pull', 'back', 'shoulders', 'cable', 'strength', 'Tarik rope kabel ke arah mata dengan siku tinggi untuk upper back.'],
            ['Chest Supported Row', 'back', 'biceps', 'machine', 'strength', 'Dada bersandar pada bantalan bangku, isolasi otot punggung tanpa membebani pinggang.'],
            ['Hyperextension (Back Extension)', 'back', 'legs', 'bodyweight', 'strength', 'Latih otot erector spinae dan gluteus.'],
            ['Straight Arm Cable Pulldown', 'back', 'core', 'cable', 'strength', 'Isolasi lats dengan lengan lurus menarik palang ke paha.'],
            ['Meadows Row', 'back', 'biceps', 'barbell', 'strength', 'Landmine row satu tangan dengan genggaman overhand pronated.'],
            ['Rack Pull', 'back', 'legs', 'barbell', 'strength', 'Deadlift parsial dari atas lutut fokus pada penebalan punggung atas.'],

            // SHOULDERS (15 exercises)
            ['Overhead Barbell Press (OHP)', 'shoulders', 'triceps', 'barbell', 'strength', 'Berdiri tegak dan dorong barbel dari atas dada lurus ke atas kepala.'],
            ['Seated Barbell Shoulder Press', 'shoulders', 'triceps', 'barbell', 'strength', 'Press barbel di atas kepala sambil duduk di bangku bersandar.'],
            ['Seated Dumbbell Shoulder Press', 'shoulders', 'triceps', 'dumbbell', 'strength', 'Duduk di bangku 90 derajat, dorong dumbbell ke atas.'],
            ['Arnold Press', 'shoulders', 'triceps', 'dumbbell', 'strength', 'Press berputar dari telapak tangan menghadap wajah menjadi menghadap depan.'],
            ['Dumbbell Lateral Raise', 'shoulders', 'traps', 'dumbbell', 'strength', 'Angkat dumbbell ke samping setinggi bahu untuk deltoid samping.'],
            ['Cable Lateral Raise', 'shoulders', 'traps', 'cable', 'strength', 'Lateral raise dengan resistensi kabel konstan.'],
            ['Dumbbell Front Raise', 'shoulders', 'chest', 'dumbbell', 'strength', 'Angkat dumbbell ke depan setinggi mata secara bergantian.'],
            ['Barbell Front Raise', 'shoulders', 'chest', 'barbell', 'strength', 'Angkat barbel ke depan setinggi bahu.'],
            ['Reverse Dumbbell Flyes', 'shoulders', 'back', 'dumbbell', 'strength', 'Membungkuk dan angkat dumbbell ke samping untuk rear deltoid.'],
            ['Reverse Pec Deck', 'shoulders', 'back', 'machine', 'strength', 'Mesin isolasi untuk melatih rear deltoid.'],
            ['Barbell Upright Row', 'shoulders', 'traps', 'barbell', 'strength', 'Tarik barbel vertikal ke arah dagu dengan siku memimpin.'],
            ['Dumbbell Shrugs', 'shoulders', 'back', 'dumbbell', 'strength', 'Angkat bahu setinggi mungkin untuk melatih otot trapezius.'],
            ['Barbell Shrugs', 'shoulders', 'back', 'barbell', 'strength', 'Shrug barbel berat di depan atau belakang tubuh.'],
            ['Pike Push Up', 'shoulders', 'triceps', 'bodyweight', 'strength', 'Push up dengan pinggul terangkat tinggi melatih bahu.'],
            ['Handstand Push Up', 'shoulders', 'triceps', 'bodyweight', 'strength', 'Push up terbalik vertikal menempel di dinding.'],

            // LEGS (18 exercises)
            ['Barbell Back Squat', 'legs', 'core', 'barbell', 'strength', 'Raja latihan kaki. Turunkan paha minimal sejajar lantai lalu dorong naik.'],
            ['Barbell Front Squat', 'legs', 'core', 'barbell', 'strength', 'Barbel di depan bahu, menargetkan otot paha depan (quadriceps).'],
            ['Romanian Deadlift (RDL)', 'legs', 'back', 'barbell', 'strength', 'Dorong pinggul ke belakang dengan lutut sedikit ditekuk melatih hamstring.'],
            ['Dumbbell Romanian Deadlift', 'legs', 'back', 'dumbbell', 'strength', 'RDL menggunakan sepasang dumbbell.'],
            ['Bulgarian Split Squat', 'legs', 'core', 'dumbbell', 'strength', 'Satu kaki di belakang diletakkan di bangku, squat dengan kaki depan.'],
            ['Leg Press', 'legs', 'glutes', 'machine', 'strength', 'Dorong platform beban dengan kedua kaki di mesin leg press.'],
            ['Hack Squat', 'legs', 'glutes', 'machine', 'strength', 'Squat pada mesin hack squat berpelindung punggung miring.'],
            ['Goblet Squat', 'legs', 'core', 'dumbbell', 'strength', 'Pegang satu dumbbell di depan dada dan lakukan squat dalam.'],
            ['Walking Dumbbell Lunges', 'legs', 'glutes', 'dumbbell', 'strength', 'Lunge melangkah maju bergantian membawa dumbbell di samping badan.'],
            ['Leg Extension', 'legs', 'none', 'machine', 'strength', 'Isolasi quadriceps dengan meluruskan kaki ke depan.'],
            ['Seated Leg Curl', 'legs', 'calves', 'machine', 'strength', 'Isolasi hamstring sambil duduk melipat kaki ke bawah.'],
            ['Lying Leg Curl', 'legs', 'calves', 'machine', 'strength', 'Isolasi hamstring dalam posisi tengkurap.'],
            ['Barbell Hip Thrust', 'legs', 'back', 'barbell', 'strength', 'Punggung di bangku, dorong barbel di atas pinggul ke atas untuk glutes.'],
            ['Standing Calf Raise', 'legs', 'none', 'machine', 'strength', 'Jinjit di mesin calf raise untuk melatih betis gastrocnemius.'],
            ['Seated Calf Raise', 'legs', 'none', 'machine', 'strength', 'Jinjit sambil duduk untuk melatih otot soleus betis.'],
            ['Sumo Deadlift', 'legs', 'back', 'barbell', 'strength', 'Kuda-kuda kaki lebar dengan tangan di antara kedua lutut.'],
            ['Bodyweight Jump Squat', 'legs', 'cardio', 'bodyweight', 'strength', 'Squat eksplosif lalu melompat ke udara.'],
            ['Step-Ups', 'legs', 'glutes', 'dumbbell', 'strength', 'Melangkah naik ke atas bangku atau boks dengan satu kaki.'],

            // ARMS - BICEPS & TRICEPS (16 exercises)
            ['Barbell Bicep Curl', 'arms', 'forearms', 'barbell', 'strength', 'Berdiri tegak dan tekuk barbel ke arah dada tanpa mengayunkan siku.'],
            ['EZ-Bar Preacher Curl', 'arms', 'forearms', 'barbell', 'strength', 'Lengan bersandar pada bantalan preacher bench untuk isolasi puncak bisep.'],
            ['Dumbbell Alternating Curl', 'arms', 'forearms', 'dumbbell', 'strength', 'Curl dumbbell bergantian dengan supinasi pergelangan tangan.'],
            ['Dumbbell Hammer Curl', 'arms', 'forearms', 'dumbbell', 'strength', 'Telapak tangan saling berhadapan melatih brachialis dan lengan bawah.'],
            ['Incline Dumbbell Curl', 'arms', 'forearms', 'dumbbell', 'strength', 'Duduk di bangku miring 60 derajat untuk peregangan maksimal bisep.'],
            ['Concentration Curl', 'arms', 'forearms', 'dumbbell', 'strength', 'Siku bertumpu di paha dalam, isolasi fokus puncak bisep.'],
            ['Cable Bicep Curl', 'arms', 'forearms', 'cable', 'strength', 'Curl menggunakan handle kabel lurus dengan tegangan konstan.'],
            ['Close-Grip Barbell Bench Press', 'arms', 'chest', 'barbell', 'strength', 'Bench press dengan jarak tangan selebar bahu fokus tricep.'],
            ['Tricep Rope Pushdown', 'arms', 'none', 'cable', 'strength', 'Dorong tali kabel ke bawah dan buka di bagian bawah kontraksi.'],
            ['Tricep Straight Bar Pushdown', 'arms', 'none', 'cable', 'strength', 'Pushdown menggunakan bar lurus membebani kepala lateral tricep.'],
            ['Skull Crushers (Lying Tricep Ext)', 'arms', 'none', 'barbell', 'strength', 'Baring dan turunkan EZ-bar perlahan ke arah dahi lalu luruskan.'],
            ['Overhead Dumbbell Tricep Extension', 'arms', 'none', 'dumbbell', 'strength', 'Pegang satu dumbbell dengan dua tangan di belakang kepala.'],
            ['Tricep Dips (Bench)', 'arms', 'chest', 'bodyweight', 'strength', 'Tangan di tepi bangku, turunkan badan dengan menekuk siku ke 90 derajat.'],
            ['Cable Overhead Tricep Extension', 'arms', 'none', 'cable', 'strength', 'Tarik kabel dari belakang kepala ke depan melatih kepala panjang tricep.'],
            ['Barbell Wrist Curl', 'arms', 'forearms', 'barbell', 'strength', 'Gulung barbel dengan pergelangan tangan melatih kekuatan cengkeraman.'],
            ['Reverse Barbell Curl', 'arms', 'forearms', 'barbell', 'strength', 'Genggaman telapak ke bawah melatih otot brachioradialis.'],

            // CORE & ABS (12 exercises)
            ['Hanging Leg Raise', 'core', 'legs', 'bodyweight', 'strength', 'Bergantung di pull up bar dan angkat kedua kaki lurus ke depan.'],
            ['Captain Chair Knee Raise', 'core', 'legs', 'bodyweight', 'strength', 'Angkat lutut ke dada menggunakan bantalan sandaran siku.'],
            ['Cable Woodchoppers', 'core', 'shoulders', 'cable', 'strength', 'Gerakan memutar torso menarik kabel menyilang melatih oblique.'],
            ['Plank', 'core', 'shoulders', 'bodyweight', 'strength', 'Tahan posisi lurus bertumpu pada lengan bawah selama mungkin.'],
            ['Side Plank', 'core', 'legs', 'bodyweight', 'strength', 'Tahan posisi miring menyamping melatih otot core lateral.'],
            ['Ab Wheel Rollout', 'core', 'back', 'bodyweight', 'strength', 'Dorong roda ab wheel ke depan dari posisi berlutut dan tarik kembali.'],
            ['Cable Crunch', 'core', 'none', 'cable', 'strength', 'Berlutut di depan katrol kabel dan tekuk tubuh ke arah bawah.'],
            ['Russian Twist', 'core', 'none', 'bodyweight', 'strength', 'Duduk dengan kaki terangkat, putar torso ke kiri dan kanan membawa beban.'],
            ['Bicycle Crunch', 'core', 'none', 'bodyweight', 'strength', 'Crunch bergantian mempertemukan siku ke lutut berlawanan.'],
            ['Mountain Climbers', 'core', 'cardio', 'bodyweight', 'cardio', 'Posisi push up dan hentakkan kaki maju mundur seperti mendaki.'],
            ['Decline Bench Sit-Up', 'core', 'none', 'bodyweight', 'strength', 'Sit up pada bangku miring ke bawah.'],
            ['Dead Bug', 'core', 'none', 'bodyweight', 'strength', 'Baring terlentang, turunkan lengan dan kaki berlawanan secara terkontrol.'],

            // CARDIO & CONDITIONING (10 exercises)
            ['Outdoor Running', 'cardio', 'legs', 'cardio', 'cardio', 'Lari luar ruangan melatih ketahanan kardiovaskular dan stamina.'],
            ['Treadmill Jog / Sprint', 'cardio', 'legs', 'machine', 'cardio', 'Lari di atas mesin treadmill dengan variasi kecepatan dan tanjakan.'],
            ['Stationary Cycling', 'cardio', 'legs', 'machine', 'cardio', 'Gowes sepeda statis dengan resistensi teratur.'],
            ['Jump Rope (Skipping)', 'cardio', 'calves', 'bodyweight', 'cardio', 'Lompat tali ritmis untuk pembakaran kalori dan koordinasi.'],
            ['Rowing Machine (Ergometer)', 'cardio', 'back', 'machine', 'cardio', 'Mendayung di mesin rowing melatih 85% otot tubuh serentak.'],
            ['Stair Climber', 'cardio', 'legs', 'machine', 'cardio', 'Menaiki tangga berputar untuk melatih glutes dan stamina jantung.'],
            ['Assault Air Bike', 'cardio', 'arms', 'machine', 'cardio', 'Sepeda angin intensitas tinggi mendorong dan menarik stang.'],
            ['Kettlebell Swings', 'cardio', 'back', 'dumbbell', 'strength', 'Ayunkan kettlebell dari antara paha setinggi bahu dengan dorongan pinggul.'],
            ['Burpees', 'cardio', 'chest', 'bodyweight', 'cardio', 'Kombinasi squat, push up, dan lompatan eksplosif ke udara.'],
            ['Battle Ropes', 'cardio', 'shoulders', 'bodyweight', 'cardio', 'Hentakkan tali tambang tebal secara berirama membentuk gelombang.'],

            // HOME WORKOUT & LIGHT EXERCISES (PUSH UP & BODYWEIGHT VARIATIONS)
            ['Knee Push Up (Push Up Lutut)', 'chest', 'core', 'bodyweight', 'strength', 'Push up bertumpu pada lutut. Variasi ringan yang ramah bagi pemula atau pemanasan di rumah.'],
            ['Diamond Push Up', 'chest', 'arms', 'bodyweight', 'strength', 'Ibu jari dan telunjuk saling bersentuhan membentuk wajik di bawah dada, fokus dada tengah dan trisep.'],
            ['Wide-Grip Push Up', 'chest', 'shoulders', 'bodyweight', 'strength', 'Push up dengan bukaan tangan lebih lebar dari bahu untuk menargetkan dada bagian luar.'],
            ['Wall Push Up (Push Up Dinding)', 'chest', 'arms', 'bodyweight', 'strength', 'Push up ringan bertumpu pada dinding, ideal untuk pemanasan ringan tanpa tekanan sendi.'],
            ['Push Up to T-Rotation', 'chest', 'core', 'bodyweight', 'strength', 'Lakukan push up lalu putar tubuh ke samping mengangkat satu tangan ke atas melatih stabilitas core.'],
            ['Archer Push Up', 'chest', 'shoulders', 'bodyweight', 'strength', 'Rentangkan satu lengan lurus ke samping saat turun untuk melatih kekuatan unilateral dada.'],
            ['Hindu Push Up', 'shoulders', 'chest', 'bodyweight', 'strength', 'Gerakan push up menukik ke bawah lalu melengkung naik melatih mobilitas bahu dan tulang belakang.'],
            ['Arm Circles (Putaran Lengan)', 'shoulders', 'none', 'bodyweight', 'cardio', 'Putar kedua lengan lurus secara melingkar ke depan dan belakang untuk mobilitas bahu ringan.'],
            ['Prone Y-T-W Raises', 'shoulders', 'back', 'bodyweight', 'strength', 'Tengkurap di lantai dan angkat kedua lengan membentuk huruf Y, T, dan W untuk bahu belakang.'],
            ['Superman (Back Extension)', 'back', 'legs', 'bodyweight', 'strength', 'Tengkurap di lantai, angkat lengan dan kaki bersamaan untuk menguatkan punggung bawah dan glutes.'],
            ['Bird-Dog', 'back', 'core', 'bodyweight', 'strength', 'Posisi merangkak di lantai, rentangkan tangan dan kaki berlawanan bergantian untuk kestabilan punggung.'],
            ['Prone Cobra', 'back', 'shoulders', 'bodyweight', 'strength', 'Tengkurap di lantai, angkat dada dan rapatkan belikat untuk memperbaiki postur tubuh bungkuk.'],
            ['Doorframe Rows (Row Kusen Pintu)', 'back', 'biceps', 'bodyweight', 'strength', 'Pegang kusen pintu kokoh dengan kedua tangan lalu tarik dada mendekati kusen.'],
            ['Bodyweight Air Squat', 'legs', 'core', 'bodyweight', 'strength', 'Squat dasar menggunakan berat badan sendiri. Turunkan pinggul sejajar lutut dan dorong naik.'],
            ['Walking Lunges (Tanpa Beban)', 'legs', 'core', 'bodyweight', 'strength', 'Melangkah maju bergantian di ruangan dengan sudut kedua lutut 90 derajat.'],
            ['Reverse Lunges (Lunge Mundur)', 'legs', 'core', 'bodyweight', 'strength', 'Melangkah mundur secara terkontrol, sangat ramah pada lutut dan mengencangkan paha serta glutes.'],
            ['Wall Sit (Duduk Dinding)', 'legs', 'core', 'bodyweight', 'strength', 'Bersandar di dinding dalam posisi paha sejajar lantai menahan kontraksi isometrik paha.'],
            ['Glute Bridge (Jembatan Pinggul)', 'legs', 'core', 'bodyweight', 'strength', 'Berbaring di lantai dengan lutut ditekuk, angkat pinggul ke atas sambil memeras otot glutes.'],
            ['Single-Leg Glute Bridge', 'legs', 'core', 'bodyweight', 'strength', 'Glute bridge dengan satu kaki terangkat lurus ke udara untuk fokus aktivasi pantat unilateral.'],
            ['Donkey Kicks', 'legs', 'none', 'bodyweight', 'strength', 'Posisi merangkak di matras, tendang satu kaki ke belakang dan ke atas untuk gluteus.'],
            ['Fire Hydrants', 'legs', 'core', 'bodyweight', 'strength', 'Posisi merangkak, angkat satu paha ke samping setinggi pinggul untuk aktivasi otot abduktor pinggul.'],
            ['Standing Calf Raise (Bodyweight)', 'legs', 'none', 'bodyweight', 'strength', 'Jinjit di lantai atau tepi anak tangga rumah untuk melatih betis tanpa alat beban.'],
            ['Cossack Squat', 'legs', 'core', 'bodyweight', 'strength', 'Squat menyamping ke satu sisi dengan kaki lainnya lurus merentang untuk mobilitas paha dalam.'],
            ['Chair / Sofa Dips', 'arms', 'chest', 'bodyweight', 'strength', 'Letakkan kedua tangan di tepi kursi atau sofa kokoh, tekuk siku 90 derajat lalu dorong tubuh naik.'],
            ['Close-Grip Push Up', 'arms', 'chest', 'bodyweight', 'strength', 'Push up dengan telapak tangan selebar bahu dan siku merapat ke rusuk untuk isolasi trisep.'],
            ['Standard Abdominal Crunches', 'core', 'none', 'bodyweight', 'strength', 'Berbaring terlentang di lantai, angkat belikat mendekati paha dengan kontraksi perut atas.'],
            ['Lying Leg Raises', 'core', 'legs', 'bodyweight', 'strength', 'Berbaring terlentang di lantai, rapatkan kedua kaki dan angkat hingga 90 derajat lalu turunkan perlahan.'],
            ['Flutter Kicks', 'core', 'legs', 'bodyweight', 'strength', 'Berbaring terlentang dan lakukan gerakan tendangan kecil naik-turun bergantian untuk perut bawah.'],
            ['Hollow Body Hold', 'core', 'legs', 'bodyweight', 'strength', 'Punggung bawah rata di matras, tahan posisi perahu cekung untuk melatih kekuatan core intristik.'],
            ['Side Plank Dip', 'core', 'legs', 'bodyweight', 'strength', 'Dari posisi side plank, turunkan pinggul mendekati lantai lalu dorong kembali naik ke atas.'],
            ['Heel Touches (Penguin)', 'core', 'none', 'bodyweight', 'strength', 'Berbaring dengan lutut ditekuk, sentuh tumit kiri dan kanan bergantian melatih otot samping perut.'],
            ['Jumping Jacks', 'cardio', 'calves', 'bodyweight', 'cardio', 'Lompat membuka kaki dan merentangkan tangan ke atas kepala secara ritmis untuk pembakaran kalori.'],
            ['High Knees (Lari di Tempat)', 'cardio', 'legs', 'bodyweight', 'cardio', 'Lari di tempat dengan mengangkat paha dan lutut setinggi pinggang secara energik.'],
            ['Butt Kicks', 'cardio', 'legs', 'bodyweight', 'cardio', 'Lari di tempat dengan mengayunkan tumit ke arah belakang hingga mendekati bokong.'],
            ['Shadow Boxing (Tinju Bayangan)', 'cardio', 'shoulders', 'bodyweight', 'cardio', 'Kombinasi pukulan jab, cross, dan hook di udara untuk melatih refleks dan kardio ringan.'],
            ['Skater Hops', 'cardio', 'legs', 'bodyweight', 'cardio', 'Melompat ke samping secara lateral dari satu kaki ke kaki lainnya melatih kelincahan dan stamina.']
        ];

        foreach ($exercises as $ex) {
            Exercise::firstOrCreate(
                ['slug' => Str::slug($ex[0])],
                [
                    'user_id' => null,
                    'name' => $ex[0],
                    'primary_muscle' => $ex[1],
                    'secondary_muscle' => $ex[2] === 'none' ? null : $ex[2],
                    'equipment' => $ex[3],
                    'category' => $ex[4],
                    'instructions' => $ex[5],
                ]
            );
        }
    }
}
