<?php

namespace Tests\Feature;

use App\Base\Exceptions\NotFoundRecordException;
use App\Base\Tests\TruncateDatabase;
use App\Helpers\Crypto;
use App\Models\User;
use App\Repositories\UserRepository;
use Exception;
use Tests\TestCase;

class UserTest extends TestCase
{
    use TruncateDatabase;

    public function test_create_one_user()
    {
        $userRepository = new UserRepository(User::class);

        $user = new User();

        $user->firstname = $this->faker->firstName();
        $user->lastname = $this->faker->lastName();
        $user->email = $this->faker->unique()->freeEmail();
        $user->password = Crypto::bcrypt('abcdef123');

        $userRepository->save($user);

        $this->assertSame($user->id, 1);
    }

    public function test_create_many_users_and_get_one()
    {
        $userRepository = new UserRepository(User::class);

        $inserts = array_map(function() use ($userRepository) {
            $model = new User();

            $model->firstname = $this->faker->firstName();
            $model->lastname = $this->faker->lastName();
            $model->email = $this->faker->unique()->freeEmail();
            $model->password = Crypto::bcrypt($this->faker->bothify('??????##'));

            $userRepository->save($model);

            return $model;
        }, range(0, 10));

        $randKey = array_rand($inserts);

        $user = $userRepository->where('id', $inserts[$randKey]->id)
            ->where('email', $inserts[$randKey]->email)
            ->getOne();
        
        $this->assertSame($user->email, $inserts[$randKey]->email);
    }

    public function test_create_many_users_and_get_all()
    {
        $userRepository = new UserRepository(User::class);
        $count = random_int(20, 50);

        for ($i = 0; $i < $count; $i++) {
            $model = new User();

            $model->firstname = $this->faker->firstName();
            $model->lastname = $this->faker->lastName();
            $model->email = $this->faker->unique()->freeEmail();
            $model->password = Crypto::bcrypt($this->faker->bothify('??????##'));

            $userRepository->save($model);
        }

        $users = $userRepository->getMany();

        $this->assertCount($count, $users);
    }

    public function test_create_many_users_and_delete_one()
    {
        $userRepository = new UserRepository(User::class);
        $count = random_int(10, 20);

        for ($i = 0; $i < $count; $i++) {
            $model = new User();

            $model->firstname = $this->faker->firstName();
            $model->lastname = $this->faker->lastName();
            $model->email = $this->faker->unique()->freeEmail();
            $model->password = Crypto::bcrypt($this->faker->bothify('??????##'));

            $userRepository->save($model);
        }

        $user = $userRepository->orderBy('createdAt', 'DESC')
            ->getOne();

        $id = $user->id;
        UserRepository::remove($user);

        try {
            $userRepository->where('id', $id)
                ->getOneOrFail();
            
            $this->assertTrue(false);
        }
        catch(Exception $e) {
            $this->assertInstanceOf(NotFoundRecordException::class, $e);
        }
    }

    public function test_create_many_users_and_delete_all()
    {
        $userRepository = new UserRepository(User::class);
        $count = random_int(10, 20);
        $toDelete = [];

        for ($i = 0; $i < $count; $i++) {
            $model = new User();

            $model->firstname = $this->faker->firstName();
            $model->lastname = $this->faker->lastName();
            $model->email = $this->faker->unique()->freeEmail();
            $model->password = Crypto::bcrypt($this->faker->bothify('??????##'));

            $userRepository->save($model);

            array_push($toDelete, $model->id);
        }

        $userRepository->whereIn('id', $toDelete)
            ->delete();

        foreach ($toDelete as $id) {
            try {
                $userRepository->where('id', $id)
                    ->getOneOrFail();

                $this->assertTrue(false);
            }
            catch(Exception $e) {
                $this->assertInstanceOf(NotFoundRecordException::class, $e);
            }
        }
    }

    public function test_create_one_user_and_update()
    {
        $userRepository = new UserRepository(User::class);

        $user = new User();

        $user->firstname = $this->faker->firstName();
        $user->lastname = $this->faker->lastName();
        $user->email = $this->faker->unique()->freeEmail();
        $user->password = Crypto::bcrypt('abcdef123');

        $userRepository->save($user);

        $userRepository->where('email', $user->email)
            ->update([
                'firstname' => 'NEW NAME',
                'lastname' => 'NEW NAME'
            ]);

        $check = $userRepository->where('email', $user->email)
            ->getOne();

        $this->assertSame($check->firstname, 'NEW NAME');
        $this->assertSame($check->lastname, 'NEW NAME');
    }
}
