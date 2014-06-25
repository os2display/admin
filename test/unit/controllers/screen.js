'use strict';
 
describe('ScreenController', function(){
    var scope; //we'll use this scope in our tests
 
    //mock Application to allow us to inject our own dependencies
    beforeEach(angular.mock.module('ikApp'));
    //mock the controller for the same reason and include $rootScope and $controller
    beforeEach(angular.mock.inject(function($rootScope, $controller){
        //create an empty scope
        scope = $rootScope.$new();
        //declare the controller and inject our empty scope
        $controller('ScreenController', {$scope: scope});
    }));
    // tests start here
    it('should have variable screen', function(){
        expect(scope.editor).toBe(undefined);
    });
});
