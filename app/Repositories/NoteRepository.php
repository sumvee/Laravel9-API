<?php


namespace App\Repositories;

use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class NoteRepository
{
    /**
     * Get all of the tasks for a given user.
     *
     * @param User $user
     * @return Collection
     */
    public function forUser(User $user)
    {
        return Note::where( 'user_id',$user->id)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function delete($id)
    {
        return Note::where( 'user_id',$id)->delete();

    }

}




