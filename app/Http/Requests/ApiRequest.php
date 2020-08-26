<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Structure;

abstract class ApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

//    /**
//     * Determine if the user has roles.
//     * @param string
//     * @return bool
//     */
//    protected function validateRole($roleName)
//    {
//        // get user role objects
//        $roleObjList = auth()->user()->roles->pluck('role_name');
//
//        // convert to array
//        $rolesArray = [];
//        foreach ($roleObjList as $role)
//        {
//            array_push($rolesArray, $role);
//        }
//
//        // get
//        $isHasRequiredRole = in_array($roleName, $rolesArray);
//
//        return $isHasRequiredRole;
//    }
//
//    /**
//     * Determine if the user has credentials for structure edit.
//     * @param integer
//     * @return bool
//     */
//    protected function validateStructureCredential($requestStructureId)
//    {
//        $validStrsArray = []; // container for full structure ids list
//
//         $legalParentStrIdList = auth()->user()->credentialStructures->pluck('id_str');
//
//        // get full-childs list for each parent id structures (as parent node of tree)
//        foreach ($legalParentStrIdList as $legalStructureId)
//        {
//            // get credential str full list by parent id
//            $strCredentialList = Structure::getAllChilds($legalStructureId);
//
//            // for each id str from credential list
//            foreach ($strCredentialList as $strId)
//            {
//                array_push($validStrsArray, $strId->id_str);
//            }
//        }
//
//        // check contains of requested structure id in credential list for current auth user
//        $isValidate = in_array($requestStructureId, $validStrsArray);
//
//        return $isValidate;
//    }
//
//    /**
//     * Determine if the user has credentials for worker edit.
//     * @param integer
//     * @return bool
//     */
//    protected function validateWorkerCredential($requestWorkeructureId)
//    {
//        // credential list for current user
//        $roleObjList = auth()->user()->credentialWorkersIds();
//
//        // chek contains requested worker id in credential list for current user
//        $isHasRequiredRole = in_array($requestWorkeructureId, $roleObjList);
//
//        return $isHasRequiredRole;
//    }
}
