<?php

namespace App\Services;

use App\Models\Topic;
use App\Models\Subject;

class TopicService
{
    public function getAll()
    {
        $topics = Topic::with('subject')->get();
        return ['success' => true, 'topics' => $topics];
    }

    public function getById($id)
    {
        $topic = Topic::with('subject')->find($id);

        if (!$topic) {
            return ['success' => false, 'message' => 'Topic not found'];
        }

        return ['success' => true, 'topic' => $topic];
    }

    public function create(array $data)
    {
        if (!Subject::find($data['subject_id'])) {
            return ['success' => false, 'message' => 'Subject does not exist'];
        }

        $topic = Topic::create($data);
        return ['success' => true, 'topic' => $topic];
    }

    public function update($id, array $data)
    {
        $topic = Topic::find($id);

        if (!$topic) {
            return ['success' => false, 'message' => 'Topic not found'];
        }

        if (isset($data['subject_id']) && !Subject::find($data['subject_id'])) {
            return ['success' => false, 'message' => 'Subject does not exist'];
        }

        $topic->update($data);
        return ['success' => true, 'topic' => $topic];
    }

    public function delete($id)
    {
        $topic = Topic::find($id);

        if (!$topic) {
            return ['success' => false, 'message' => 'Topic not found'];
        }

        $topic->delete();
        return ['success' => true, 'message' => 'Topic Deleted Successfully'];
    }
}
