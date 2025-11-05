<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\Stack;
use App\Models\Subject;
use App\Models\Topic;

class AdminExamService
{
    public function getAll(array $filters)
    {
        $query = Exam::with(['stack', 'subject', 'topic']);

        // Filter: stack
        if (!empty($filters['stack_id'])) {
            $query->where('stack_id', $filters['stack_id']);
        }

        // Filter: subject
        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        // Filter: topic
        if (!empty($filters['topic_id'])) {
            $query->where('topic_id', $filters['topic_id']);
        }

        // Search by title or description
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $filters['per_page'] ?? 10;
        $page = $filters['page'] ?? 1;

        $paginated = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'success' => true,
            'data' => $paginated
        ];
    }


    public function getById($id)
    {
        $exam = Exam::with(['stack', 'subject', 'topic'])->find($id);

        if (!$exam) {
            return ['success' => false, 'message' => 'Exam not found'];
        }

        return ['success' => true, 'exam' => $exam];
    }

    public function create(array $data)
    {
        // Validate stack
        if (!Stack::find($data['stack_id'])) {
            return ['success' => false, 'message' => 'Stack does not exist'];
        }

        // Validate subject (optional)
        if (isset($data['subject_id']) && $data['subject_id'] !== null) {
            if (!Subject::find($data['subject_id'])) {
                return ['success' => false, 'message' => 'Subject does not exist'];
            }
        }

        // Validate topic (optional)
        if (isset($data['topic_id']) && $data['topic_id'] !== null) {
            if (!Topic::find($data['topic_id'])) {
                return ['success' => false, 'message' => 'Topic does not exist'];
            }
        }

        $exam = Exam::create($data);

        return ['success' => true, 'exam' => $exam];
    }

    public function update($id, array $data)
    {
        $exam = Exam::find($id);

        if (!$exam) {
            return ['success' => false, 'message' => 'Exam not found'];
        }

        // Validate stack
        if (isset($data['stack_id']) && !Stack::find($data['stack_id'])) {
            return ['success' => false, 'message' => 'Stack does not exist'];
        }

        // Validate subject
        if (isset($data['subject_id']) && $data['subject_id'] !== null) {
            if (!Subject::find($data['subject_id'])) {
                return ['success' => false, 'message' => 'Subject does not exist'];
            }
        }

        // Validate topic
        if (isset($data['topic_id']) && $data['topic_id'] !== null) {
            if (!Topic::find($data['topic_id'])) {
                return ['success' => false, 'message' => 'Topic does not exist'];
            }
        }

        $exam->update($data);

        return ['success' => true, 'exam' => $exam];
    }

    public function delete($id)
    {
        $exam = Exam::find($id);

        if (!$exam) {
            return ['success' => false, 'message' => 'Exam not found'];
        }

        $exam->delete();

        return ['success' => true];
    }
}
