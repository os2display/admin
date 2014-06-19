'use strict';
 
describe('SlideController', function(){
    var scope; //we'll use this scope in our tests
 
    //mock Application to allow us to inject our own dependencies
    beforeEach(angular.mock.module('ikApp'));
    //mock the controller for the same reason and include $rootScope and $controller
    beforeEach(angular.mock.inject(function($rootScope, $controller){
        //create an empty scope
        scope = $rootScope.$new();
        //declare the controller and inject our empty scope
        $controller('SlideController', {$scope: scope});
    }));
    // tests start here
    it('should have variable slide', function(){
        expect(scope.editor).toBe(undefined);
    });
});
