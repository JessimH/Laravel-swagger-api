<?php

namespace App\Http\Controllers;
use App\Models\Task;
use Illuminate\Http\Request;

class TasksController extends Controller
{
    public function tasks(Request $request){
        /**
         * @OA\Get(path="/api/tasks",
         *   tags={"tasks"},
         *   summary="User Tasks",
         *   description="User Tasks",
         *   operationId="UserAllTasks",
         *   security={ {"bearerAuth": {}} },
         *  @OA\Response(
        *    response=200,
        *    description="Success",
        *    @OA\JsonContent(
        *       @OA\Property(property="name", type="string", ref="#/components/schemas/Task"),  
        *        )
        *     ),
        *    @OA\Response(
        *    response=401,
        *    description="error",
        *    @OA\JsonContent(
        *       @OA\Property(property="message", type="string", example="Unauthorized"),
        *        )
        *     ),
        * )
        */

        /**
         * @OA\Get(path="/api/tasks?completed={completed}",
         *   tags={"tasks"},
         *   summary="User Tasks",
         *   description="User Tasks",
         *   operationId="allTasksUser",
         *   security={ {"bearerAuth": {}} },
         *   @OA\Parameter(
        *    description="Task completed or not",
        *    in="path",
        *    name="completed",
        *    example="true",
        *    @OA\Schema(
        *       type="boolean"
        *    )
        * ),
        *  @OA\Response(
        *    response=200,
        *    description="Success",
        *    @OA\JsonContent(
        *       @OA\Property(property="name", type="string", ref="#/components/schemas/Task"),  
        *        )
        *     ),
        *   @OA\Response(
        *    response=404,
        *    description="error",
        *    @OA\JsonContent(
        *       @OA\Property(property="message", type="string", example="Page not found"),
        *        )
        *     ),
        *    @OA\Response(
        *    response=401,
        *    description="error",
        *    @OA\JsonContent(
        *       @OA\Property(property="message", type="string", example="Unauthorized"),
        *        )
        *     ),
        * )
        */

        if(!$request->completed){
            $tasks = Task::where('user_id', $request->user()->id)->get();
            return response()->json([
                $tasks
            ], 200);
        }

        else if($request->completed == 'true' || $request->completed == 'false'){
            $tasks = Task::where('user_id', $request->user()->id)->where('completed', $request->completed)->get();
            return response()->json([
                $tasks
            ], 200);
        }

        else{
            return response()->json([
                'page not found'
            ], 404);
        }
    }

    public function addTask(Request $request){
        /**
         * @OA\Post(path="/api/addTask",
         *   summary="Create a task",
         *   tags={"tasks"},
         *   description="Create a task",
         *   operationId="addTask",
         *   security={ {"bearerAuth": {}} },
         * @OA\RequestBody(
        *    required=true,
        *    description="Body",
        *    @OA\JsonContent(
        *       required={"body"},
        *       @OA\Property(property="body", type="string", ref="#/components/schemas/Task/properties/body"),
        *    ),
        * ),
        *  @OA\Response(
        *    response=201,
        *    description="Success",
        *    @OA\JsonContent(
        *       @OA\Property(property="name", type="string", ref="#/components/schemas/Task"), 
        *        )
        *     ),
        *   @OA\Response(
        *    response=400,
        *    description="error",
        *    @OA\JsonContent(
        *       @OA\Property(property="message", type="string", example="Tous les champs sont obligatoire"),
        *        )
        *     ),
        *    @OA\Response(
        *    response=401,
        *    description="error",
        *    @OA\JsonContent(
        *       @OA\Property(property="message", type="string", example="Unauthorized"),
        *        )
        *     ),
        * )
        */

        $request->validate([
            'body' => 'required'
        ]);

        $task = Task::create([
            'body' => $request->body,
            'user_id'=>$request->user()->id,
        ]);

        if(!$request->body){
            return response()->json([
                "success"=> false,
                "message"=> "Tous les champs sont obligatoire"
            ], 400);
        }

        return response()->json([
            $task
        ], 201);
    }

    public function deleteTask(Request $request, $id){
        /**
         * @OA\Delete(path="/api/deleteTask/{id}",
         *   tags={"tasks"},
         *   summary="Delete user task",
         *   description="Delete user task",
         *   operationId="deleteUserTasks",
         *   security={ {"bearerAuth": {}} },
         * @OA\Parameter(
        *    description="task ID",
        *    in="path",
        *    name="id",
        *    required=true,
        *    example="1",
        *    @OA\Schema(
        *       type="integer",
        *       format="int64",
        *       ref="#/components/schemas/Task/properties/id"
        *    )
        * ),
        *  @OA\Response(
        *    response=200,
        *    description="Success",
        *    @OA\JsonContent(
        *       @OA\Property(property="success", type="string", example="true"),
        *        )
        *     ),
        *    @OA\Response(
        *    response=403,
        *    description="error",
        *    @OA\JsonContent(
        *       @OA\Property(property="message", type="string", example="Vous n'êtes pas authorisé a supprimer cette tache"),
        *        )
        *     ),
        *   @OA\Response(
        *    response=404,
        *    description="error",
        *    @OA\JsonContent(
        *       @OA\Property(property="message", type="string", example="Cette tache n'existe pas"),
        *        )
        *     ),
        *    @OA\Response(
        *    response=401,
        *    description="error",
        *    @OA\JsonContent(
        *       @OA\Property(property="message", type="string", example="Unauthorized"),
        *        )
        *     ),
        * )
        */

        $task = Task::find($id);

        if(!$task){
            return response()->json([
                "message"=> "Cette tache n'existe pas"
            ], 404);
        }

        if($request->user()->id != $task->user_id){
            return response()->json([
                "message"=> "Vous n'êtes pas authorisé a supprimer cette tache"
            ], 403);
        }

        $task_deleted = Task::where('id', $id)->delete();

        return response()->json([
            "success"=>true
        ], 200);
    }

    public function updateTask(Request $request, $id){
        /**
        * @OA\Put(path="/api/updateTask/{id}",
        *   tags={"tasks"},
        *   summary="Update user task",
        *   description="Update user task",
        *   operationId="updateUserTasks",
        *   security={ {"bearerAuth": {}} },
        *   @OA\RequestBody(
        *    required=true,
        *    description="Body",
        *    @OA\JsonContent(
        *       required={"body"},
        *       @OA\Property(property="body", type="string", ref="#/components/schemas/Task/properties/body"),
        *    ),
        * ),
        * @OA\Parameter(
        *    description="task ID",
        *    in="path",
        *    name="id",
        *    required=true,
        *    example="1",
        *    @OA\Schema(
        *       type="integer",
        *       format="int64",
        *       ref="#/components/schemas/Task/properties/id"
        *    )
        * ),
        *  @OA\Response(
        *    response=200,
        *    description="Success",
        *    @OA\JsonContent(
        *       @OA\Property(property="name", type="string", ref="#/components/schemas/Task"), 
        *        )
        *     ),
        *   @OA\Response(
        *    response=400,
        *    description="error",
        *    @OA\JsonContent(
        *       @OA\Property(property="message", type="string", example="Tous les champs sont obligatoire"),
        *        )
        *     ),
        *    @OA\Response(
        *    response=404,
        *    description="error",
        *    @OA\JsonContent(
        *       @OA\Property(property="message", type="string", example="Cette tache n'existe pas"),
        *        )
        *     ),
        *    @OA\Response(
        *    response=403,
        *    description="error",
        *    @OA\JsonContent(
        *       @OA\Property(property="message", type="string", example="Vous n'êtes pas authorisé a modifier cette tache"),
        *        )
        *     ),
        *    @OA\Response(
        *    response=401,
        *    description="error",
        *    @OA\JsonContent(
        *       @OA\Property(property="message", type="string", example="Unauthorized"),
        *        )
        *     ),
        * )
        */

        $task = Task::find($id);
        if(!$task){
            return response()->json([
                "message"=> "Cette tache n'existe pas"
            ], 404);
        }

        if($request->user()->id != $task->user_id){
            return response()->json([
                "message"=> "Vous n'êtes pas authorisé a modifier cette tache"
            ], 403);
        }

        $request->validate([
            'body' => 'required'
        ]);

        $task_updated = Task::where('id', $id)->update([
            'body'=> $request->body 
            ]);
        return response()->json([
            "success"=>true
        ], 200);
    }

     public function checkTask(Request $request, $id){
         /**
        * @OA\Get(path="/api/checkTask/{id}",
        *   tags={"tasks"},
        *   summary="Complete task of user",
        *   description="Complete task of user",
        *   operationId="checkTasksUser",
        *   security={ {"bearerAuth": {}} },
        * @OA\Parameter(
        *    description="ID of task",
        *    in="path",
        *    name="id",
        *    required=true,
        *    example="1",
        *    @OA\Schema(
        *       type="integer",
        *       format="int64",
        *       ref="#/components/schemas/Task/properties/id"
        *    )
        * ),
        *  @OA\Response(
        *    response=200,
        *    description="Success",
        *    @OA\JsonContent(
        *       @OA\Property(property="success", type="string", example="true"),
        *        )
        *     ),
        *    @OA\Response(
        *    response=404,
        *    description="error",
        *    @OA\JsonContent(
        *       @OA\Property(property="message", type="string", example="Cette tache n'existe pas"),
        *        )
        *     ),
        *    @OA\Response(
        *    response=403,
        *    description="error",
        *    @OA\JsonContent(
        *       @OA\Property(property="message", type="string", example="Vous n'êtes pas authorisé a remplir cette tache"),
        *        )
        *     ),
        *    @OA\Response(
        *    response=401,
        *    description="error",
        *    @OA\JsonContent(
        *       @OA\Property(property="message", type="string", example="Unauthorized"),
        *        )
        *     ),
        * )
        */

        $task = Task::find($id);
        if(!$task){
            return response()->json([
                "message"=> "Cette tache n'existe pas"
            ], 404);
        }

        if($request->user()->id != $task->user_id){
            return response()->json([
                "message"=> "Vous n'êtes pas authorisé a remplir cette tache"
            ], 403);
        }

        $task_updated = Task::where('id', $id)->update([
            'completed'=> true 
        ]);

        return response()->json([
            "success"=>true
        ], 200);
     }
}
