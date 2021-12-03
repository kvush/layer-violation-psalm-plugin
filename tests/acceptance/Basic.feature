Feature: basics
  In order to test my plugin
  As a plugin developer
  I need to have tests

  Background:
    Given I have the following config
      """
      <?xml version="1.0"?>
      <psalm totallyTyped="true">
        <projectFiles>
          <directory name="."/>
        </projectFiles>
        <plugins>
          <pluginClass class="Kvush\LayerViolationPsalmPlugin\Plugin">
            <context>
              <common>
                <acceptable name="JetBrains\PhpStorm" />
              </common>
              <layer name="App\Domain\ContextA">
                <acceptable name="App\Domain\ContextA" />
                <acceptable name="App\DateTime" />
                <acceptable name="App\EntityId" />
              </layer>
              <layer name="App\Domain\ContextB">
                <acceptable name="App\Domain\ContextB" />
                <acceptable name="App\EntityId" />
              </layer>
            </context>
          </pluginClass>
        </plugins>
      </psalm>
      """
  Scenario: run without errors
    Given I have the following code
      """
      <?php
      namespace App\Domain\ContextA\Some\Model;

      use JetBrains\PhpStorm;
      use App\DateTime;
      use App\Domain\ContextA\Another\Model;
      """
    When I run Psalm
    Then I see no errors

  Scenario: run with errors
    Given I have the following code
      """
      <?php
      namespace App\Domain\ContextA\Some\Model;

      use JetBrains\PhpStorm;
      use App\Domain\ContextB\Alien\Model;
      """
    When I run Psalm
    Then I see these errors
      | Type                     | Message                                                   |
      | LayerDependencyViolation | Not allowed dependency for App\Domain\ContextA\Some\Model |
    And I see no other errors

