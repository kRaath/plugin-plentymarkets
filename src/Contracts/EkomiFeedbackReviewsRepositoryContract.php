<?php
 
namespace EkomiFeedback\Contracts;
 
use EkomiFeedback\Models\EkomiFeedbackReviews;
 
/**
 * Class ReviewsRepositoryContract
 * @package EkomiFeedback\Contracts
 */
interface EkomiFeedbackReviewsRepositoryContract
{
    /**
     * Add a new task to the To Do list
     *
     * @param array $data
     * @return EkomiFeedbackReviews
     */
    public function createTask(array $data): EkomiFeedbackReviews;
 
    /**
     * List all tasks of the To Do list
     *
     * @return EkomiFeedbackReviews[]
     */
    public function getReviewsList(): array;
 
    /**
     * Update the status of the task
     *
     * @param int $id
     * @return EkomiFeedbackReviews
     */
    public function updateTask($id): EkomiFeedbackReviews;
 
    /**
     * Delete a task from the To Do list
     *
     * @param int $id
     * @return EkomiFeedbackReviews
     */
    public function deleteTask($id): EkomiFeedbackReviews;
}